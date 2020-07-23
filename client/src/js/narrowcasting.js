import Reveal from 'reveal.js';

{
  if ('revealConfig' in window) {
    Reveal.initialize(window.revealConfig);
    window.Reveal = Reveal;

    Reveal.addEventListener('slidechanged', function (event) {
      // check if refresh is needed on the first slide
      if (event.indexh === 0 && event.indexv === 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.href);
        xhr.setRequestHeader('x-requested-with', 'XMLHttpRequest');
        xhr.onload = function() {
          if (xhr.status === 200 && shouldReload(xhr.responseText)) {
            window.location.reload();
          }
        };

        xhr.send();
      }
    });
  } else {
    console.error('No config is set');
  }
}

function shouldReload(xhrResponse) {
  const response = JSON.parse(xhrResponse);
  let checks = ['presentationID', 'lastEdited'];
  checks = checks.filter(function (value) {
    return response.hasOwnProperty(value) && window.hasOwnProperty(value) && response[value] !== window[value];
  });

  // Return true when any of the checks pass
  return checks.length !== 0;
}
