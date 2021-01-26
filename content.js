var elements = document.querySelectorAll('a');

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('mouseover', function(event) {
        chrome.extension.sendRequest({"url": this.href});
    });
}
