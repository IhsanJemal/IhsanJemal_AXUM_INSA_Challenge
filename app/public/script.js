// Toggle dark mode on double-click anywhere
document.addEventListener("dblclick", () => {
  document.body.classList.toggle("dark");
});

// Add subtle animation when typing in inputs
document.querySelectorAll("input, textarea").forEach(el => {
  el.addEventListener("focus", () => {
    el.style.borderColor = "#1abc9c";
    el.style.boxShadow = "0 0 6px rgba(26, 188, 156, 0.5)";
  });
  el.addEventListener("blur", () => {
    el.style.borderColor = "#ccc";
    el.style.boxShadow = "none";
  });
});

// Button click ripple effect
document.querySelectorAll("button").forEach(btn => {
  btn.addEventListener("click", e => {
    let ripple = document.createElement("span");
    ripple.className = "ripple";
    ripple.style.left = e.offsetX + "px";
    ripple.style.top = e.offsetY + "px";
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });
});
