(function () {
  const TC = (window.TC = window.TC || {});
  const api = TC.api;

  const me = async () => {
    return api.get("/backend/api/users/me.php");
  };

  TC.profile = { me };
})();
