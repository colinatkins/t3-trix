var __importDefault = this && this.__importDefault || function(e) {
    return e && e.__esModule ? e : {
        default: e
    }
};
define(["require", "exports", "jquery", 'TYPO3/CMS/Recordlist/LinkBrowser', 'TYPO3/CMS/Backend/Modal'], (function(e, i, t, LinkBrowser, Modal) {
    "use strict";
    Object.defineProperty(i, "__esModule", {
        value: !0
    }), i.TrixBridge = void 0, t = __importDefault(t);
    i.TrixBridge = class {
        static modal = Modal;
    }

    window.TrixBridge = i.TrixBridge;
}));