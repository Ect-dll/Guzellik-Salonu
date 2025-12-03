// Basit istemci tarafı güvenlik duvarı (WAF benzeri)
(function () {
  const LOG_KEY = "fwLogs";
  const BLOCK_PATTERNS = [
    /<\s*script/gi,
    /on\w+\s*=/gi,
    /\b(select|insert|update|delete|drop|union|exec)\b/gi,
    /(\.\.\/)+/g,
    /--|;|\/\*|\*\//g,
    /javascript:/gi
  ];
  const RATE_LIMIT_WINDOW = 10000;
  const RATE_LIMIT_MAX = 5;
  const MAX_FIELD_LENGTH = 500;
  const rateMap = new Map();

  function readLogs() {
    try {
      return JSON.parse(localStorage.getItem(LOG_KEY) || "[]");
    } catch (_) {
      return [];
    }
  }

  function writeLogs(logs) {
    try {
      localStorage.setItem(LOG_KEY, JSON.stringify(logs.slice(-50)));
    } catch (_) {
      /* ignore quota issues */
    }
  }

  function logEvent(type, details) {
    const logs = readLogs();
    logs.push({ type, details, ts: new Date().toISOString() });
    writeLogs(logs);
  }

  function rateLimited(key) {
    const now = Date.now();
    const history = rateMap.get(key) || [];
    const fresh = history.filter((ts) => now - ts < RATE_LIMIT_WINDOW);
    fresh.push(now);
    rateMap.set(key, fresh);
    return fresh.length > RATE_LIMIT_MAX;
  }

  function sanitizeValue(value) {
    if (!value || typeof value !== "string") return value;
    return value.replace(/[<>]/g, "");
  }

  function isSuspicious(value) {
    if (!value || typeof value !== "string") return false;
    if (value.length > MAX_FIELD_LENGTH) return true;
    return BLOCK_PATTERNS.some((regex) => regex.test(value));
  }

  function notifyUser(msg) {
    console.warn("[Firewall]", msg);
  }

  function inspectField(field) {
    const value = field.value;
    if (isSuspicious(value)) {
      field.value = sanitizeValue(value);
      field.classList.add("fw-blocked");
      return true;
    }
    field.classList.remove("fw-blocked");
    return false;
  }

  function handleInput(event) {
    const target = event.target;
    if (!target || !["INPUT", "TEXTAREA"].includes(target.tagName)) return;
    inspectField(target);
  }

  function handleSubmit(event) {
    const form = event.target;
    if (!form || !(form instanceof HTMLFormElement)) return;
    const fields = Array.from(form.querySelectorAll("input, textarea"));
    const blocked = fields.filter(inspectField);
    const rateKey = form.getAttribute("action") || form.id || "form";
    if (blocked.length || rateLimited(rateKey)) {
      event.preventDefault();
      notifyUser("Şüpheli form girişi engellendi.");
      logEvent("form_block", {
        form: rateKey,
        blockedFields: blocked.map((f) => f.name || f.id || "field")
      });
    }
  }

  function inspectUrl(url) {
    if (typeof url !== "string") return false;
    return /javascript:|data:/i.test(url) || /\.\.\//.test(url);
  }

  function inspectPayload(payload) {
    if (!payload) return false;
    if (typeof payload === "string") return isSuspicious(payload);
    if (payload instanceof FormData) {
      for (const value of payload.values()) {
        if (isSuspicious(String(value))) return true;
      }
    }
    if (typeof payload === "object") {
      return Object.values(payload).some((v) => isSuspicious(String(v)));
    }
    return false;
  }

  function wrapFetch() {
    if (!window.fetch) return;
    const originalFetch = window.fetch.bind(window);
    window.fetch = async function (resource, config = {}) {
      const suspiciousUrl = inspectUrl(typeof resource === "string" ? resource : resource.url);
      const suspiciousBody = inspectPayload(config.body);
      if (suspiciousUrl || suspiciousBody) {
        logEvent("fetch_block", { url: resource, cause: suspiciousUrl ? "url" : "body" });
        notifyUser("Şüpheli ağ isteği engellendi.");
        return Promise.reject(new Error("Firewall engelledi"));
      }
      if (rateLimited(`fetch:${resource}`)) {
        logEvent("fetch_rate_limit", { url: resource });
        return Promise.reject(new Error("Çok fazla istek"));
      }
      return originalFetch(resource, config);
    };
  }

  function wrapXHR() {
    if (!window.XMLHttpRequest) return;
    const open = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function (method, url, ...rest) {
      if (inspectUrl(url)) {
        logEvent("xhr_block", { url, method });
        notifyUser("Şüpheli XHR engellendi.");
        throw new Error("Firewall engelledi");
      }
      return open.call(this, method, url, ...rest);
    };
  }

  document.addEventListener("input", handleInput, true);
  document.addEventListener("submit", handleSubmit, true);
  wrapFetch();
  wrapXHR();

  window.ClientFirewall = {
    getLogs: () => readLogs(),
    clearLogs: () => {
      localStorage.removeItem(LOG_KEY);
      rateMap.clear();
    }
  };
})();

