let links = {
    order_page: '',
    application_domain: ''
};

async function initializePayment(encrypted_data, linksResponse) {
    console.log(JSON.parse(linksResponse));
    links.application_domain = JSON.parse(linksResponse)['application_domain'];
    links.order_page = JSON.parse(linksResponse)['order_page'];

    let dados = JSON.parse(window.atob(encrypted_data));

    await Swal.fire({
        title: 'Confirm the number to be used',
        input: 'number',
        type: 'question',
        inputValue: dados['tel'],
        allowOutsideClick: false,
        confirmButton: 'OK',
        showCancelButton: true,
        inputValidator: (number) => {
            if (!number.match(/^(85|84)[0-9]{7}$/)) {
                return "Enter a valid number";
            }
        }
    }).then(function (response) {
        if (response['value']) {
            this.processPayment(response.value, encrypted_data, links.application_domain);
            console.log(response.value);
        } else {
            console.log("cancelled");
            console.log(response);
        }
    });
}

function processPayment(numero, encrypted_data) {
    this.showLoading();
    axios.defaults.headers.post['Accepts'] = 'application/json';
    axios.defaults.headers.post['Content-Type'] = 'application/json';

    axios.post(links.application_domain + '/?initialize_payment=1', {
        'numero': numero,
        'encrypted_data': encrypted_data,
        headers: {
            'Content-Type': 'application/json',
            'Accepts': 'application/json'
        }
    }).then((response) => {
        if (response.status === 200) {
            Swal.close();

            console.log(response);

            let responseData = JSON.parse(response.data);

            console.log(responseData);
            this.showMessageToUser(responseData);
        } else {
            console.error("show error message");
        }
    }).catch((error) => {
        Swal.close();
        this.disableButton(false);
        this.showErrorMessage("An unexpected error occurred");
        console.log(error);
        if (error.response) {
            // the request was made and the server responded with a status code
            console.log({ errorResponse: error.response.data });
            console.log({ errorStatus: error.response.status });
            console.log({ errorHeaders: error.response.headers });
        } else if (error.request) {
            // the request was made, but no response was received
            console.log({ errorRequest: error });
        } else {
            // something happened in setting up the request
            console.log({ errorMessage: error.message });
        }
    });
}

function showMessageToUser(response) {
    this.disableButton(false);
    switch (response['output_ResponseCode']) {
        case 'INS-0': {
            this.showSuccessMessage("Payment successful", response['output_ResponseCode'], response['output_ResponseDesc']);
            this.disableButton(true);
            break;
        }
        case 'INS-5': {
            this.showErrorMessage("Transaction Cancelled by Customer");
            break;
        }
        case 'INS-9': {
            this.showErrorMessage("The transaction took too long, please try again");
            break;
        }
        case 'INS-10': {
            this.showErrorMessage("The transaction is already in progress, please wait a moment");
            break;
        }
        case 'INS-2001': {
            this.showErrorMessage("Wrong PIN, please try again");
            break;
        }
        case 'INS-2006': {
            this.showErrorMessage("Insufficient balance to complete the purchase");
            break;
        }
        case 'INS-996': {
            this.showErrorMessage("Client's Mpesa account is not active");
            break;
        }
        case 'INS-6': {
            this.showErrorMessage("Transaction failed, please try again");
            break;
        }
        default: {
            this.showErrorMessage("An unexpected error occurred, please try again");
            break;
        }
    }
}

function showSuccessMessage(message, responseCode, responseDescription) {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        confirmButtonText: 'Finish',
        html: 'Congratulations, click <strong>Finish</strong> to continue',
        type: 'success',
    }).then(function () {
        this.finalizePayment(responseCode, responseDescription);
    });
}

function showErrorMessage(message) {
    Swal.fire({
        title: message,
        text: 'Please try again',
        type: 'error',
        allowOutsideClick: false,
    });
}

/**
 * Finalize the Payment
 */
function finalizePayment(code, description) {
    Swal.fire({
        title: "Finalizing...",
        text: "Your order is being finalized",
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });
    console.log('Processed');
    axios.post(links.application_domain + "/?payment_action", {
        code: code,
        description: description
    }).then((response) => {
        Swal.close();
        if (response.status === 200) {
            window.location.href = links.order_page;
        }
    }).catch((error) => {
        Swal.close();
        console.log(error);
        this.showErrorMessage("An error occurred while finalizing");
    });
}

/**
 * Show three dots loading
 */
function showLoading() {
    Swal.fire({
        title: "Processing...",
        text: "Please check your mobile phone",
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });
}

function disableButton(flag) {
    document.getElementById('pay_btn').disabled = flag;
}
