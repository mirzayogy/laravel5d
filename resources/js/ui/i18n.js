// Interface text in the browser. The server sends the dictionary (window.I18N) only for
// languages other than English; English text is the key, so a missing entry simply shows English.
export const t = (key, vars = {}) =>
    String((window.I18N && window.I18N[key]) ?? key).replace(/:([a-z]+)/g, (m, name) => (name in vars ? vars[name] : m));
