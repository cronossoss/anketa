document.querySelectorAll(".role-toggle").forEach((toggle) => {
  toggle.addEventListener("change", function () {
    let id = this.dataset.id;
    let checkbox = this;
    let badge = this.closest("td").querySelector(".role-badge");

    fetch("/anketa/admin/users.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "ajax_toggle_role=" + id + "&csrf=<?= csrf_token() ?>",
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "ok") {
          if (data.role === "admin") {
            badge.classList.remove("bg-secondary");
            badge.classList.add("bg-danger");
            badge.innerText = "Admin";
          } else {
            badge.classList.remove("bg-danger");
            badge.classList.add("bg-secondary");
            badge.innerText = "User";
          }
        } else {
          alert("Greška");
          checkbox.checked = !checkbox.checked;
        }
      })
      .catch(() => {
        alert("Greška konekcije");
        checkbox.checked = !checkbox.checked;
      });
  });
});
