/*
 * This file is derived from the official rte_ckeditor extension.
 * All rights belong to their respective owners.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
import LinkBrowser from "@typo3/backend/link-browser.js";
import Modal from "@typo3/backend/modal.js";
class TrixLinkBrowser {
    constructor() {
        this.editor = null, this.selectionStartPosition = null, this.selectionEndPosition = null
    }
    initialize(t) {
        this.editor = Modal.currentModal.userData.trix;
        Modal.currentModal.addEventListener('typo3-modal-hide', () => {
            this.editor.deactivateButtonIfNoAttributeSet()
        });
    }
    finalizeFunction(t) {
        const e = LinkBrowser.getLinkAttributeValues(),
            i = e.params ? e.params : "";
        delete e.params;
        const n = this.convertAttributes(e, "");
        this.editor.linkSet(this.sanitizeLink(t, i), n), Modal.dismiss()
    }
    convertAttributes(t, e) {
        const i = {
            attrs: {}
        };
        for (const [e, n] of Object.entries(t)) ['href', 'title', 'class', 'target', 'rel'].includes(e) && (i.attrs[this.addLinkPrefix(e)] = n);
        return "string" == typeof e && "" !== e && (i.linkText = e), i
    }
    addLinkPrefix(attribute)  {
        const capitalizedAttribute = attribute.charAt(0).toUpperCase() + attribute.slice(1);
        return 'link' + capitalizedAttribute;
    }
    sanitizeLink(t, e) {
        console.log(t, e)
        const i = t.match(/^([a-z0-9]+:\/\/[^:\/?#]+(?:\/?[^?#]*)?)(\??[^#]*)(#?.*)$/);
        if (i && i.length > 0) {
            t = i[1] + i[2];
            const n = i[2].length > 0 ? "&" : "?";
            e.length > 0 && ("&" === e[0] && (e = e.substr(1)), e.length > 0 && (t += n + e)), t += i[3]
        }
        return t
    }
}
let trixLinkBrowser = new TrixLinkBrowser;
export default trixLinkBrowser;
LinkBrowser.finalizeFunction = t => {
    trixLinkBrowser.finalizeFunction(t)
};