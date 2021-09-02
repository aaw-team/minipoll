
function initializeMinipoll(event) {
    let elements = document.querySelectorAll("div.tx_minipoll-poll[data-minipoll-ajax]");
    for (let i=0; i<elements.length; i++) {
        let pollContainer = elements[i];
        let ajaxUris = {
            detail: pollContainer.dataset.minipollAjaxDetail,
            results: pollContainer.dataset.minipollAjaxResults,
            vote: pollContainer.dataset.minipollAjaxVote
        };

        let requestUri = ajaxUris.detail;
        if (pollContainer.dataset.minipollAjax == 'results') {
            requestUri = ajaxUris.results;
        }

        // Fetch poll data
        makeGETRequest(requestUri, pollContainer);
    }
}

function addMinipollEventListeners(data, pollContainer) {
    let showResultButtons = pollContainer.querySelectorAll("[data-minipoll-showresult]");
    if (showResultButtons.length > 0) {
        for (j=0; j<showResultButtons.length; j++) {
            showResultButtons[j].addEventListener('click', (event) => {
                event.preventDefault();
                makeGETRequest(pollContainer.dataset.minipollAjaxResults, pollContainer);
            });
        }
    }
    let showDetailButtons = pollContainer.querySelectorAll("[data-minipoll-detail]");
    if (showDetailButtons.length > 0) {
        for (j=0; j<showDetailButtons.length; j++) {
            showDetailButtons[j].addEventListener('click', (event) => {
                event.preventDefault();
                makeGETRequest(pollContainer.dataset.minipollAjaxDetail, pollContainer);
            });
        }
    }
    let voteForms = pollContainer.querySelectorAll("form[data-minipoll-vote]");
    if (voteForms.length > 0) {
        for (j=0; j<voteForms.length; j++) {
            voteForms[j].addEventListener('submit', (event) => {
                event.preventDefault();
                makePOSTRequest(pollContainer.dataset.minipollAjaxVote, new URLSearchParams(new FormData(event.target)), pollContainer);
            });
        }
    }
}

function makeGETRequest(requestUri, pollContainer) {
    fetch(requestUri, {
        credentials: 'same-origin',
        mode: 'same-origin',
        referrerPolicy: 'same-origin'
    })
    .then(response => {
        //console.log('Got response', response);
        return response.json()
    })
    .then(data => {
        //console.log('Got data', data);
        if (data.messages && data.messages.length > 0) {
            for (y=0; y < data.messages.length; y++) {
                console.info('Got a message from server: "' + data.messages[y] + '"');
            }
        }
        if (data.poll && data.poll.html) {
            pollContainer.innerHTML = data.poll.html;
            addMinipollEventListeners(data, pollContainer);
        } else {
            console.error('Error: no poll data received', data);
        }
    });
}

function makePOSTRequest(requestUri, body, pollContainer) {
    // Send poll data
    fetch(requestUri, {
        method: 'POST',
        headers: {
          // 'Content-Type': 'application/json'
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body,
        credentials: 'same-origin',
        mode: 'same-origin',
        referrerPolicy: 'same-origin',
        cache: 'no-cache',
        redirect: 'error'
    })
    .then(response => {
        //console.log('Got response', response);
        return response.json()
    })
    .then(data => {
        //console.log('Got data', data);
        if (data.messages && data.messages.length > 0) {
            for (y=0; y < data.messages.length; y++) {
                console.info('Got a message from server: "' + data.messages[y] + '"');
            }
        }
        if (data.poll && data.poll.html) {
            pollContainer.innerHTML = data.poll.html;
            addMinipollEventListeners(data, pollContainer);
        } else {
            console.info('Error: no poll data received', data);
        }

        // Re-load captcha
        if (pollContainer.dataset.minipollCaptcha === 'sr_freecap' && typeof SrFreecap === 'object') {
            let captchaElements = pollContainer.querySelectorAll("[id^=tx_srfreecap_captcha_image_]");
            if (captchaElements.length === 1) {
                SrFreecap.newImage(captchaElements[0].id.split('tx_srfreecap_captcha_image_')[1]);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initializeMinipoll);
