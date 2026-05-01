(function () {
  const TC = (window.TC = window.TC || {});

  const toQuery = (params) => {
    if (!params) return "";
    const usp = new URLSearchParams();
    for (const [k, v] of Object.entries(params)) {
      if (v === undefined || v === null || v === "") continue;
      usp.set(k, String(v));
    }
    const s = usp.toString();
    return s ? `?${s}` : "";
  };

  const toForm = (data) => {
    const usp = new URLSearchParams();
    for (const [k, v] of Object.entries(data || {})) {
      if (v === undefined || v === null) continue;
      usp.set(k, String(v));
    }
    return usp;
  };

  const safeMessage = (err) => {
    if (!err) return "请求失败";
    if (typeof err === "string") return err;
    if (err.message) return err.message;
    return "请求失败";
  };

  const request = async (path, { method = "GET", params, data } = {}) => {
    const url = `${path}${toQuery(params)}`;

    const init = {
      method,
      credentials: "include",
      headers: {},
    };

    if (method !== "GET" && method !== "HEAD") {
      init.headers["Content-Type"] = "application/x-www-form-urlencoded;charset=UTF-8";
      init.body = toForm(data);
    }

    const resp = await fetch(url, init);

    let json = null;
    try {
      json = await resp.json();
    } catch (e) {
      json = null;
    }

    if (!resp.ok) {
      const msg = (json && json.message) || `HTTP ${resp.status}`;
      const err = new Error(msg);
      err.status = resp.status;
      err.payload = json;
      throw err;
    }

    if (!json || typeof json.success !== "boolean") {
      throw new Error("接口返回不是合法 JSON");
    }

    if (!json.success) {
      throw new Error(json.message || "操作失败");
    }

    return json.data || {};
  };

  const util = {
    clear(el) {
      while (el.firstChild) el.removeChild(el.firstChild);
    },
    getQueryParam(key) {
      const u = new URL(location.href);
      return u.searchParams.get(key);
    },
    formatTime(s) {
      if (!s) return "";
      const d = new Date(s);
      if (Number.isNaN(d.getTime())) return String(s);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const day = String(d.getDate()).padStart(2, "0");
      const hour = String(d.getHours()).padStart(2, "0");
      const minute = String(d.getMinutes()).padStart(2, "0");
      return `${year}-${month}-${day} ${hour}:${minute}`;
    },
  };

  const ui = {
    _flashEl: null,
    bindFlash(el) {
      ui._flashEl = el;
    },
    flash(msg, type) {
      const el = ui._flashEl;
      if (!el) return;
      el.className = `flash${type ? ` ${type}` : ""}`;
      el.textContent = msg || "";
    },
    flashError(err) {
      ui.flash(safeMessage(err), "error");
    },
    renderNav(navEl, user) {
      util.clear(navEl);

      const mkLink = (href, text) => {
        const a = document.createElement("a");
        a.className = "link";
        a.href = href;
        a.textContent = text;
        return a;
      };

      const leftNav = document.createElement("div");
      leftNav.style.display = "flex";
      leftNav.style.gap = "22px";
      leftNav.style.alignItems = "center";

      leftNav.appendChild(mkLink("./index.html", "首页"));

      if (user) {
        leftNav.appendChild(mkLink("./profile.html", "个人中心"));

        const logoutBtn = document.createElement("button");
        logoutBtn.type = "button";
        logoutBtn.className = "btn";
        logoutBtn.textContent = "退出";
        logoutBtn.addEventListener("click", async () => {
          try {
            await TC.auth.logout();
            location.href = "./index.html";
          } catch (e) {
            ui.flashError(e);
          }
        });
        leftNav.appendChild(logoutBtn);
      } else {
        leftNav.appendChild(mkLink("./login.html", "登录"));
        leftNav.appendChild(mkLink("./register.html", "注册"));
      }

      navEl.appendChild(leftNav);
    },
  };

  TC.api = {
    request,
    get(path, params) {
      return request(path, { method: "GET", params });
    },
    post(path, data) {
      return request(path, { method: "POST", data });
    },
  };

  TC.util = util;
  TC.ui = ui;
})();
