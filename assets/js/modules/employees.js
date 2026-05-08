document.addEventListener("DOMContentLoaded", () => {
  initEmployeeSearch();
  initEditButtons();
});

function initEmployeeSearch() {
  const search = document.getElementById("employeeSearch");

  if (!search) return;

  search.addEventListener("keyup", function () {
    const value = this.value.toLowerCase();

    document.querySelectorAll(".employee-row").forEach((row) => {
      row.style.display = row.innerText.toLowerCase().includes(value)
        ? ""
        : "none";
    });
  });
}

function initEditButtons() {
  document.querySelectorAll(".edit-employee-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;

      console.log("Edit employee:", id);
    });
  });
}
