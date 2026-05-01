(function () {
  const TC = (window.TC = window.TC || {});
  const api = TC.api;

  const searchPosts = async (q, { page, page_size } = {}) => {
    return api.get("/backend/api/search/search.php", { q, page, page_size });
  };

  TC.search = { searchPosts };
})();
