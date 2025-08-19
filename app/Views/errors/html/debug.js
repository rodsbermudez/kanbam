/*
 *---------------------------------------------------------------
 * DEBUG TOOLBAR
 *---------------------------------------------------------------
 *
 * This is the CSS file for the debug toolbar.
 */

function init() {
    var tabs = document.getElementById('tabs');

    if (! tabs) {
        return;
    }

    var tabLinks = tabs.querySelectorAll('a');

    for (var i = 0; i < tabLinks.length; i++) {
        tabLinks[i].addEventListener('click', function(e) {
            e.preventDefault();

            // Make all tabs inactive
            for (var j = 0; j < tabLinks.length; j++) {
                tabLinks[j].classList.remove('active');
                document.getElementById(tabLinks[j].getAttribute('href').substr(1)).classList.remove('active');
            }

            // Make this tab active
            this.classList.add('active');
            document.getElementById(this.getAttribute('href').substr(1)).classList.add('active');
        });
    }
}

function toggle(elem) {
    elem = document.getElementById(elem);
    elem.style.display = (elem.style.display !== 'none' ? 'none' : 'block' );
    return false;
}