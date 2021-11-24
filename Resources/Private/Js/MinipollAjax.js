var Minipoll = function(container) {
    this.container = container;
    this.content = container.querySelectorAll('[data-minipoll-content]')[0];
    this.messages = container.querySelectorAll('[data-minipoll-messages]')[0];
    this.ajaxUris = {
        detail: this.container.dataset.minipollAjaxDetail,
        results: this.container.dataset.minipollAjaxResults,
        vote: this.container.dataset.minipollAjaxVote
    };

    if (this.container.dataset.minipollAjax == 'results') {
        this.getResults();
    } else {
        this.getDetail();
    }

    return this;
};

Minipoll.prototype.getDetail = function() {
    this.makeGETRequest(this.ajaxUris.detail);
};
Minipoll.prototype.getResults = function() {
    this.makeGETRequest(this.ajaxUris.results);
};
Minipoll.prototype.postVote = function(data) {
    this.makePOSTRequest(this.ajaxUris.vote, data);
};

Minipoll.prototype.addMinipollEventListeners = function(data) {
    let showResultButtons = this.container.querySelectorAll("[data-minipoll-showresult]");
    if (showResultButtons.length > 0) {
        for (j=0; j<showResultButtons.length; j++) {
            showResultButtons[j].addEventListener('click', (event) => {
                event.preventDefault();
                this.deleteMessages();
                this.getResults();
            });
        }
    }
    let showDetailButtons = this.container.querySelectorAll("[data-minipoll-detail]");
    if (showDetailButtons.length > 0) {
        for (j=0; j<showDetailButtons.length; j++) {
            showDetailButtons[j].addEventListener('click', (event) => {
                event.preventDefault();
                this.deleteMessages();
                this.getDetail();
            });
        }
    }
    let voteForms = this.container.querySelectorAll("form[data-minipoll-vote]");
    if (voteForms.length > 0) {
        for (j=0; j<voteForms.length; j++) {
            voteForms[j].addEventListener('submit', (event) => {
                event.preventDefault();
                this.deleteMessages();
                this.postVote(new URLSearchParams(new FormData(event.target)));
            });
        }
    }
};

Minipoll.prototype.makeGETRequest = function(requestUri) {
    // start spinner
    this.container.classList.add('tx_minipoll-loading');

    fetch(requestUri, {
        credentials: 'same-origin',
        mode: 'same-origin',
        referrerPolicy: 'same-origin'
    })
    .then(response => {
        return response.json()
    })
    .then(data => {
        //console.log('Got data', data);
        // if (data.messages && data.messages.length) {
        //     for (y=0; y < data.messages.length; y++) {
        //         this.showMessage(data.messages[y], true);
        //         console.info('Got a message from server: "' + data.messages[y] + '"');
        //     }
        // }
        if (data.poll && data.poll.html) {
            this.content.innerHTML = data.poll.html;
            this.addMinipollEventListeners(data);
        } else if (data.messages && data.messages.length) {
            for (y=0; y < data.messages.length; y++) {
                this.showMessage(data.messages[y], true);
                console.info('Got a message from server: "' + data.messages[y] + '"');
            }
        } else {
            this.showMessage('Fehler: keine Poll Daten erhalten');
            console.error('No poll data received', data);
        }

        // Inform other code
        this.container.dispatchEvent(new CustomEvent('minipoll_get', {
            bubbles: true,
            detail: { poll: () => data.poll }
        }));

        // check for svgpiechart
        if (this.container.querySelectorAll('.tx_minipoll-svgpiechart').length) {
            $(this.container.querySelectorAll('.tx_minipoll-svgpiechart')[0]).svgpiechart();
        }

        // stop spinner
        this.container.classList.remove('tx_minipoll-loading');
    });
};

Minipoll.prototype.makePOSTRequest = function(requestUri, body) {
    // start spinner
    this.container.classList.add('tx_minipoll-loading');

    // Send poll data
    fetch(requestUri, {
        method: 'POST',
        headers: {
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
        return response.json()
    })
    .then(data => {
        // if (data.messages && data.messages.length > 0) {
        //     for (y=0; y < data.messages.length; y++) {
        //         this.showMessage(data.messages[y], true);
        //         console.info('Got a message from server: "' + data.messages[y] + '"');
        //     }
        // }
        if (data.poll && data.poll.html) {
            this.content.innerHTML = data.poll.html;
            this.addMinipollEventListeners(data);
        } else if (data.messages && data.messages.length) {
            for (y=0; y < data.messages.length; y++) {
                this.showMessage(data.messages[y], true);
                console.info('Got a message from server: "' + data.messages[y] + '"');
            }
        } else {
            this.showMessage('Fehler: keine Poll Daten erhalten');
            console.error('No poll data received', data);
            this.makeGETRequest(this.container.dataset.minipollAjaxDetail);
        }

        // Inform other code
        this.container.dispatchEvent(new CustomEvent('minipoll_post', {
            bubbles: true,
            detail: { poll: () => data.poll }
        }));

        // stop spinner
        this.container.classList.remove('tx_minipoll-loading');
    });
};

Minipoll.prototype.showMessage = function(message, ok) {
    let div = document.createElement('div');
    if (ok) {
        div.classList.add('tx_minipoll-ok');
    }
    div.innerText = message;
    this.messages.appendChild(div);
};
Minipoll.prototype.deleteMessages = function() {
    this.messages.innerHTML = "";
};

let minipolls = document.querySelectorAll("div.tx_minipoll-poll[data-minipoll-ajax]");
for (let i=0; i<minipolls.length; i++) {
    new Minipoll(minipolls[i]);
}
