(function () {
  const TC = (window.TC = window.TC || {});
  const api = TC.api;

  let cachedMe = null;

  const me = async () => {
    const data = await api.get("/backend/api/auth/me.php");
    cachedMe = data.user || null;
    return data;
  };

  const login = async ({ username, password }) => {
    const data = await api.post("/backend/api/auth/login.php", { username, password });
    cachedMe = data.user || null;
    return data;
  };

  const register = async ({ username, password, nickname }) => {
    const data = await api.post("/backend/api/auth/register.php", { username, password, nickname });
    return data;
  };

  const logout = async () => {
    await api.post("/backend/api/auth/logout.php", {});
    cachedMe = null;
  };

  const requireAuth = async (loginUrl, nextUrl) => {
    try {
      await me();
      return true;
    } catch (e) {
      const next = nextUrl || location.href;
      const u = new URL(loginUrl, location.href);
      u.searchParams.set("next", next);
      location.href = u.toString();
      throw e;
    }
  };

  TC.auth = {
    me,
    login,
    register,
    logout,
    requireAuth,
    get cachedUser() {
      return cachedMe;
    },
  };
})();
