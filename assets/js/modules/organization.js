const typeSelect = document.getElementById("type");
const parentSelect = document.getElementById("parentSelect");

if (typeSelect && parentSelect) {
  typeSelect.addEventListener("change", filterParents);

  function filterParents() {
    const type = typeSelect.value;

    [...parentSelect.options].forEach((option) => {
      option.hidden = false;

      const parentType = option.dataset.type;

      if (!parentType) return;

      if (type === "OC") {
        option.hidden = true;
      } else if (type === "OJ") {
        option.hidden = parentType !== "OC";
      } else if (type === "OD") {
        option.hidden = parentType !== "OJ";
      }
    });

    if (type === "OC") {
      parentSelect.value = "";
    }
  }

  filterParents();
}
