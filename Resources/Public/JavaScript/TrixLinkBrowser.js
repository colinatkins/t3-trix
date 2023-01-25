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
define(["require", "exports", "TYPO3/CMS/Recordlist/LinkBrowser", "TYPO3/CMS/Backend/Modal"], (function(t, exports, LinkBrowser, Modal) {
    "use strict";
    class TrixLinkBrowser {
        constructor() {
            this.editor = null;
        }
        initialize(t) {
            this.editor = Modal.currentModal.data("trix");
            Modal.currentModal.on('hide.bs.modal', () => {
                this.editor.deactivateButtonIfNoAttributeSet();
            });
            // if (void 0 !== e) this.CKEditor = e;
            // else {
            //     let e;
            //     e = void 0 !== top.TYPO3.Backend && void 0 !== top.TYPO3.Backend.ContentContainer.get() ? top.TYPO3.Backend.ContentContainer.get() : window.parent, i.default.each(e.CKEDITOR.instances, (e, i) => {
            //         i.id === t && (this.CKEditor = i)
            //     })
            // }
            // window.addEventListener("beforeunload", () => {
            //     this.CKEditor.getSelection().selectRanges(this.ranges)
            // }), this.ranges = this.CKEditor.getSelection().getRanges(), i.default.extend(l, (0, i.default)("body").data()), (0, i.default)(".t3js-class-selector").on("change", () => {
            //     (0, i.default)("option:selected", this).data("linkTitle") && (0, i.default)(".t3js-linkTitle").val((0, i.default)("option:selected", this).data("linkTitle"))
            // }), (0, i.default)(".t3js-removeCurrentLink").on("click", t => {
            //     t.preventDefault(), this.CKEditor.execCommand("unlink"), s.dismiss()
            // })
        }
        finalizeFunction(t) {
            const e = LinkBrowser.getLinkAttributeValues(),
                i = e.params ? e.params : "";
            delete e.params;
            const n = this.convertAttributes(e, "");
            this.editor.linkSet(this.sanitizeLink(t, i), n), Modal.dismiss();
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
    return LinkBrowser.finalizeFunction = t => {
        trixLinkBrowser.finalizeFunction(t)
    }, trixLinkBrowser
}));