(function () {
  const TC = (window.TC = window.TC || {});
  const api = TC.api;

  const list = async ({ page, page_size } = {}) => {
    return api.get("/backend/api/posts/list.php", { page, page_size });
  };

  const detail = async (id) => {
    return api.get("/backend/api/posts/detail.php", { id });
  };

  const create = async ({ title, content }) => {
    return api.post("/backend/api/posts/create.php", { title, content });
  };

  const remove = async (id) => {
    return api.post("/backend/api/posts/delete.php", { id });
  };

  const toggleLike = async (id) => {
    return api.post("/backend/api/posts/toggle_like.php", { id });
  };

  const renderPostCard = (post, { onLike } = {}) => {
    const wrap = document.createElement("article");
    wrap.className = "post-card";

    const h = document.createElement("h2");
    h.className = "post-title";

    const a = document.createElement("a");
    a.href = `./post.html?id=${encodeURIComponent(post.id)}`;
    a.textContent = post.title || "无标题随笔";
    h.appendChild(a);

    const preview = document.createElement("div");
    preview.className = "content";
    const t = (post.content_preview || post.content || "").trim();
    const mark = document.createElement("span");
    mark.className = "summaryMark";
    mark.textContent = "摘要：";
    preview.appendChild(mark);
    preview.appendChild(document.createTextNode(t.length > 180 ? `${t.slice(0, 180)}…` : t));

    const meta = document.createElement("div");
    meta.className = "meta";
    meta.innerHTML = `posted @ ${TC.util.formatTime(post.created_at)} 匿名园友 评论(${post.comment_count || 0}) 推荐(${post.like_count || 0})`;

    wrap.appendChild(h);
    wrap.appendChild(preview);
    wrap.appendChild(meta);

    wrap._tc = { meta };
    return wrap;
  };

  const updatePostCard = (el, post) => {
    if (!el || !el._tc) return;
    el._tc.meta.innerHTML = `posted @ ${TC.util.formatTime(post.created_at)} 匿名园友 评论(${post.comment_count || 0}) 推荐(${post.like_count || 0})`;
  };

  TC.posts = { list, detail, create, remove, toggleLike, renderPostCard, updatePostCard };
})();
