(function () {
  const TC = (window.TC = window.TC || {});
  const api = TC.api;

  const create = async ({ post_id, content }) => {
    return api.post("/backend/api/comments/create.php", { post_id, content });
  };

  const remove = async (id) => {
    return api.post("/backend/api/comments/delete.php", { id });
  };

  const toggleLike = async (id) => {
    return api.post("/backend/api/comments/toggle_like.php", { id });
  };

  const renderCommentCard = (c, { onLike, onDelete, showPostLink } = {}) => {
    const wrap = document.createElement("article");
    wrap.className = "comment-card";

    const content = document.createElement("div");
    content.className = "content";
    content.style.marginBottom = "8px";
    content.textContent = c.content || "";

    const meta = document.createElement("div");
    meta.className = "meta";
    meta.style.textAlign = "left";
    meta.style.paddingBottom = "0";
    meta.style.borderBottom = "0";

    let metaText = `posted @ ${TC.util.formatTime(c.created_at)} 匿名园友 推荐(${c.like_count || 0})`;

    if (showPostLink && c.post_id) {
      meta.innerHTML = `${metaText} <a href="./post.html?id=${encodeURIComponent(c.post_id)}">查看原帖</a>`;
    } else {
      meta.textContent = metaText;
    }

    const actions = document.createElement("div");
    actions.className = "row";
    actions.style.marginTop = "8px";

    const likeBtn = document.createElement("button");
    likeBtn.type = "button";
    likeBtn.className = "btn";
    likeBtn.textContent = c.liked ? "已推荐" : "推荐";
    likeBtn.addEventListener("click", async () => {
      if (typeof onLike === "function") await onLike();
    });

    actions.appendChild(likeBtn);

    if (c.can_delete && typeof onDelete === "function") {
      const delBtn = document.createElement("button");
      delBtn.type = "button";
      delBtn.className = "btn btn-danger";
      delBtn.textContent = "删除";
      delBtn.addEventListener("click", async () => {
        const ok = confirm("确认删除这条评论？");
        if (!ok) return;
        await onDelete();
      });
      actions.appendChild(delBtn);
    }

    wrap.appendChild(content);
    wrap.appendChild(meta);
    wrap.appendChild(actions);

    wrap._tc = { likeBtn, meta };
    return wrap;
  };

  const updateCommentCard = (el, c) => {
    if (!el || !el._tc) return;
    el._tc.likeBtn.textContent = c.liked ? "已推荐" : "推荐";
    const metaText = `posted @ ${TC.util.formatTime(c.created_at)} 匿名园友 推荐(${c.like_count || 0})`;
    if (el._tc.meta.querySelector("a")) {
      const link = el._tc.meta.querySelector("a");
      el._tc.meta.innerHTML = `${metaText} `;
      el._tc.meta.appendChild(link);
    } else {
      el._tc.meta.textContent = metaText;
    }
  };

  TC.comments = { create, remove, toggleLike, renderCommentCard, updateCommentCard };
})();
