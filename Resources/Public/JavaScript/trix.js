import Trix from "@typo3/trix.esm.min.js";
const { lang } = Trix.config;
import { Application, Controller } from "@typo3/stimulus.js";
import { default as modalObject } from "@typo3/backend/modal.js";
window.Stimulus = Application.start()

Stimulus.register("trix", class extends Controller {
    static targets = [ "editor", "linkbutton" ]
    static values = {
        linkBrowserUrl: String,
        headingTagname: String
    }

    connect() {
        document.addEventListener('trix-before-initialize', () => {
            this.initTrix()
        })
        document.addEventListener('trix-selection-change', (e) => {
            this.toggleLinkButtonActiveState()
        })
    }

    initTrix () {
        Trix.config.blockAttributes.heading1.tagName = this.headingTagnameValue
        Trix.config.toolbar.getDefaultHTML = this.getDefaultHTML.bind(this)
        this.customizingTextAttributesConfig()
    }

    customizingTextAttributesConfig() {
        Trix.config.textAttributes.title = {
            groupTagName: 'a',
            parser(element) {
                const matchingSelector = `a:not(${Trix.config.attachments.attachmentSelector})`
                const link = element.closest(matchingSelector)
                if (link) {
                    return link.getAttribute("title")
                }
            },
        }
        Trix.config.textAttributes.target = {
            groupTagName: 'a',
            parser(element) {
                const matchingSelector = `a:not(${Trix.config.attachments.attachmentSelector})`
                const link = element.closest(matchingSelector)
                if (link) {
                    return link.getAttribute("target")
                }
            },
        }
    }

    toggleLinkButtonActiveState() {
        if (this.editorTarget.editor.attributeIsActive('href')) {
            this.activateButton(this.linkbuttonTarget)
        } else {
            this.deactivateButton(this.linkbuttonTarget)
        }
    }

    deactivateButtonIfNoAttributeSet() {
        if (!this.editorTarget.editor.attributeIsActive('href')) {
            this.deactivateButton(this.linkbuttonTarget)
        }
    }

    activateButton(target) {
        if (!target.classList.contains('trix-active')) {
            target.classList.add('trix-active')
        }
    }

    deactivateButton(target) {
        if (target.classList.contains('trix-active')) {
            target.classList.remove('trix-active')
        }
    }

    makeUrlFromModulePath(url, additionalParams) {
        return url + (-1 === url.indexOf("?") ? "?" : "&") + "&contentsLanguage=en&editorId=123" + (additionalParams || "")
    }

    recordAction() {
        return this.editorTarget.editor.recordUndoEntry("Formatting", { context: 'href', consolidatable: true })
    }

    toggleLink() {
        if (this.editorTarget.editor.attributeIsActive('href')) {
            this.deactivateButton(this.linkbuttonTarget)
            this.recordAction()
            this.editorTarget.editor.deactivateAttribute('href')
        } else {
            this.activateButton(this.linkbuttonTarget)
            let modalTitle = 'Link Browser'
            modalObject.advanced({
                type: modalObject.types.iframe,
                title: modalTitle,
                content: this.makeUrlFromModulePath(this.linkBrowserUrlValue, ''),
                size: modalObject.sizes.large,
                callback: t => {
                    t.userData.trix = this;
                }
            })
        }
    }

    linkSet(link, attributes) {
        this.recordAction()
        this.editorTarget.editor.activateAttribute('href', link)
        if (attributes.attrs["linkTarget"])
            this.editorTarget.editor.activateAttribute('target', attributes.attrs["linkTarget"])
        if (attributes.attrs["linkTitle"])
            this.editorTarget.editor.activateAttribute('title', attributes.attrs["linkTitle"])
    }

    getDefaultHTML () {
        return `<div class="trix-button-row">
      <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="${lang.bold}" tabindex="-1">${lang.bold}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="${lang.italic}" tabindex="-1">${lang.italic}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="${lang.strike}" tabindex="-1">${lang.strike}</button>
        ${this.linkBrowserButtons}
      </span>
      <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="${lang.heading1}" tabindex="-1">${lang.heading1}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="${lang.quote}" tabindex="-1">${lang.quote}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="${lang.code}" tabindex="-1">${lang.code}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="${lang.bullets}" tabindex="-1">${lang.bullets}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="${lang.numbers}" tabindex="-1">${lang.numbers}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="${lang.outdent}" tabindex="-1">${lang.outdent}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="${lang.indent}" tabindex="-1">${lang.indent}</button>
      </span>
      <span class="trix-button-group-spacer"></span>
      <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="${lang.undo}" tabindex="-1">${lang.undo}</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="${lang.redo}" tabindex="-1">${lang.redo}</button>
      </span>
    </div>`
    }

    get linkBrowserButtons () {
        return `<button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-action="click->trix#toggleLink" data-trix-target="linkbutton" title="${lang.link}" tabindex="-1">${lang.link}</button>`
    }
})