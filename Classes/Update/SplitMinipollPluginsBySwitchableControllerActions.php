<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\Update;

/*
 * Copyright 2021 Agentur am Wasser | Maeder & Partner AG
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\RepeatableInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * SplitMinipollPluginsBySwitchableControllerActions
 */
class SplitMinipollPluginsBySwitchableControllerActions implements UpgradeWizardInterface, ChattyInterface, RepeatableInterface
{
    /**
     * @param OutputInterface $output
     */
    protected $output;

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\ChattyInterface::setOutput()
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return self::class;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::getTitle()
     */
    public function getTitle(): string
    {
        return '[EXT:minipoll] Migrate Minipoll plugins away from switchableControllerActions';
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::getDescription()
     */
    public function getDescription(): string
    {
        return 'Because Switchable Controller Actions will not be supported anymore, the plugin configurations need to be split up in two separate plugins. This wizard takes care of this, and updates the stored FlexForm settings.';
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::updateNecessary()
     */
    public function updateNecessary(): bool
    {
        // Select affected content elements
        $qb = $this->getContentElementSelectionQueryBuilder();
        $qb->selectLiteral(
            $this->getConnectionForTable()->getDatabasePlatform()->getCountExpression('*') . ' AS ' . $qb->quoteIdentifier('count')
        );
        return $qb->execute()->fetch()['count'] > 0;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::getPrerequisites()
     */
    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Install\Updates\UpgradeWizardInterface::executeUpdate()
     */
    public function executeUpdate(): bool
    {
        // Select affected content elements
        $qb = $this->getContentElementSelectionQueryBuilder();
        $results = $qb->execute()->fetchAll();
        $updates = 0;
        if (!empty($results)) {
            try {
                $this->getConnectionForTable()->beginTransaction();

                foreach ($results as $row) {
                    // Show some detailed information
                    $this->output->write('Analyzing content element #' . $row['uid'], false, OutputInterface::VERBOSITY_VERBOSE);
                    if ($row[$GLOBALS['TCA']['tt_content']['ctrl']['delete']]) {
                        $this->output->write(' <options=bold>[deleted]</>', false, OutputInterface::VERBOSITY_VERBOSE);
                    }
                    if ($row[$GLOBALS['TCA']['tt_content']['ctrl']['languageField']] > 0) {
                        $this->output->write(' (which is a translation of CE #' . $row[$GLOBALS['TCA']['tt_content']['ctrl']['transOrigPointerField']] . ')', false, OutputInterface::VERBOSITY_VERBOSE);
                    }
                    $this->output->write('... ', false, OutputInterface::VERBOSITY_VERBOSE);

                    // Migrate the row
                    $migratedRow = $this->migratePlugin($row);
                    if ($migratedRow === null) {
                        $this->output->writeln('nothing to do.', OutputInterface::VERBOSITY_VERBOSE);
                    } else {
                        $localUpdates = $this->getConnectionForTable()->update(
                            'tt_content',
                            $migratedRow,
                            [
                                'uid' => $row['uid'],
                            ]
                        );
                        $updates += $localUpdates;

                        if ($localUpdates < 1) {
                            $this->output->writeln('nothing changed in database.', OutputInterface::VERBOSITY_VERBOSE);
                        } else {
                            $this->output->writeln('updated database.', OutputInterface::VERBOSITY_VERBOSE);
                        }
                    }
                }
                $this->getConnectionForTable()->commit();
            } catch (\Throwable $e) {
                $this->getConnectionForTable()->rollBack();
                throw $e;
            }
        }

        if ($updates > 0) {
            $this->output->writeln('Successfully updated ' . $updates . ' row' . ($updates > 1 ? 's' : ''));
        } else {
            $this->output->writeln('Nothing to be updated');
        }

        return true;
    }

    /**
     * @param array $row
     * @return array|null
     */
    protected function migratePlugin(array $row): ?array
    {
        $flexFormArray = GeneralUtility::xml2array($row['pi_flexform']);
        if (
            !is_array($flexFormArray)
            || !is_array($flexFormArray['data'])
            || !is_array($flexFormArray['data']['sDEF'])
            || !is_array($flexFormArray['data']['sDEF']['lDEF'])
        ) {
            return null;
        }

        $isOldStylePlugin = is_array($flexFormArray['data']['sDEF']['lDEF']['switchableControllerActions']);
        $isOldStyleDetailsPlugin = $isOldStylePlugin && $flexFormArray['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'] === 'Poll->detail;Poll->vote;Poll->showResult';

        if ($isOldStyleDetailsPlugin) {
            // Change list_type
            $row['list_type'] = 'minipoll_polldetail';
        }

        // Remove sDEF->switchableControllerActions
        if (isset($flexFormArray['data']['sDEF']['lDEF']['switchableControllerActions'])) {
            unset($flexFormArray['data']['sDEF']['lDEF']['switchableControllerActions']);
        }

        // Update the flexform string for the record
        $row['pi_flexform'] = GeneralUtility::makeInstance(FlexFormTools::class)->flexArray2Xml($flexFormArray, true);

        return $row;
    }

    /**
     * @return QueryBuilder
     */
    protected function getContentElementSelectionQueryBuilder(): QueryBuilder
    {
        // Select all tt_content, of CType teams_team
        $qb = $this->getConnectionForTable('tt_content')->createQueryBuilder();
        $qb->getRestrictions()->removeAll();
        $qb
        ->select('*')
        ->from('tt_content')
        ->where(
            $qb->expr()->andX(
                $qb->expr()->eq('CType', $qb->createNamedParameter('list', \PDO::PARAM_STR)),
                $qb->expr()->eq('list_type', $qb->createNamedParameter('minipoll_poll', \PDO::PARAM_STR)),
                $qb->expr()->like('pi_flexform', $qb->createNamedParameter('%switchableControllerActions%', \PDO::PARAM_STR))
            )
        );
        return $qb;
    }

    /**
     * @param string $tableName
     * @return Connection
     */
    protected function getConnectionForTable(?string $tableName = null): Connection
    {
        if ($tableName === null) {
            return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        }
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }
}
