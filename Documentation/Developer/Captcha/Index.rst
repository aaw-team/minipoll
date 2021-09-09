.. include:: ../../Includes.txt

.. _section-developer-captcha:

===============
Captcha support
===============

Minipoll is able to work with any captcha implementation. For this sake, the
interface ``AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface`` is
provided, as well as an eays system to add user defined captchas.

.. _section-developer-captcha-addowncaptcha:

Add your own captchaProvider
============================

Add a captchaProvider class, that implements the CaptchaProvider interface. For
further explanation of the interface, see the code, the methods are documented
there.

.. code-block:: php

    namespace My\Extension\CaptchaProvider;
    
    class MyFancyCaptchaProvider implements \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface
    {
        // ...
    }

Next, add a view partial in ``Form/Captcha/MyFancyCaptchaProvider.html``. The
name is composed by the "base path" ``Form/Captcha``, followed by the last part
of the class name (``My\Extension\CaptchaProvider\MyFancyCaptchaProvider``
becomes ``MyFancyCaptchaProvider``).

The partial will be invoked inside the voting form. Within the partial, a
variable ``{captcha}``, which is an array, is available. Its contents come from
your CaptchaProviders method ``createCaptchaArray()``.

In order for the captcha to work correctly, you must include a textfield with
the name "captcha" in your partial.

Example:

.. code-block:: php

    namespace My\Extension\CaptchaProvider;
    use AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface;
    use AawTeam\Minipoll\Domain\Model\Poll;
    
    class MyFancyCaptchaProvider implements CaptchaProviderInterface
    {
        public function createCaptchaArray(Poll $poll)
        {
            return [
                'src' => $this->getCaptchaSrc($poll)
            ];
        }
    }

And the partial:

.. code-block:: html

    <div>
        <img src="{captcha.src}" alt="" />
        <p>Enter the text from the image..</p>
    </div>
    <div>
        <f:form.textfield name="captcha" value="" />
    </div>

Having done that, you can register your CaptchaProvider class with an alias
("myfancy" in this case) in ``ext_localconf.php``:

.. code-block:: php

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['captchaProviders']['myfancy'] =
        \My\Extension\CaptchaProvider\MyFancyCaptchaProvider::class;

By now, you can either select your CaptchaProvider as ``defaultCaptchaProvider``
in the extension manager configuration or directly via TypoScript:

.. code-block:: typoscript

    plugin.tx_minipoll.settings.captcha = myfancy

