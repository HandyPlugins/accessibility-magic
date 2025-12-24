const {AccessibilityToolkit} = window;


import {Accessibility} from 'accessibility/dist/main.bundle';

window.addEventListener('load', function () {
		const accessibility = new Accessibility(AccessibilityToolkit);
	}, false
);
