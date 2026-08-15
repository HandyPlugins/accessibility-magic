const { AccessibilityToolkit } = window;

import { Accessibility } from 'accessibility/dist/main.bundle';
import { patchReset } from './reset';

window.addEventListener(
	'load',
	function () {
		const accessibility = new Accessibility(AccessibilityToolkit);
		patchReset(accessibility);
	},
	false
);
