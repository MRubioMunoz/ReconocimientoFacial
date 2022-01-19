/* global fetch, URLSearchParams */
const urlParams = new URLSearchParams(window.location.search);
const name = urlParams.get('name');
let url = 'https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env/service.php?name=' + name;
fetch(url)
.then(function(response) {
    return response.json();
})
.then(function (data) {
    console.log('Request succeeded with JSON response', data);
})
.catch(function (error) {
    console.log('Request failed', error);
});