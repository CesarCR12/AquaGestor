// JavaScript file (handleResponse.js)
document.getElementById("sign_in_form").addEventListener("submit", function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.status === 'success') {
            window.location.href = '../pages/index.html';
        } else {
            document.getElementById("error-message").classList.remove("d-none");
            document.getElementById("error-message").textContent = data.message;
        }
    })
    .catch(error => console.error('Error:', error));
});
