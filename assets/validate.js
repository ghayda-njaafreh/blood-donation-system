// assets/validate.js
(function(){
  // Donor form validation
  const donorForm = document.getElementById("donorForm");
  if (donorForm) {
    donorForm.addEventListener("submit", function(e){
      const name = document.getElementById("full_name")?.value?.trim() || "";
      const phone = document.getElementById("phone")?.value?.trim() || "";
      const city = document.getElementById("city")?.value?.trim() || "";
      const blood = document.getElementById("blood_type")?.value?.trim() || "";
      const age = document.getElementById("age")?.value?.trim() || "";

      let errors = [];
      if (name.length < 3) errors.push("Full name must be at least 3 characters.");
      if (!phone.match(/^[0-9\+\-\s]{7,}$/)) errors.push("Phone looks invalid.");
      if (city.length < 2) errors.push("City is required.");
      if (!blood) errors.push("Blood type is required.");
      if (age && (!age.match(/^\d+$/) || parseInt(age,10) < 16 || parseInt(age,10) > 80)) {
        errors.push("Age must be a number between 16 and 80.");
      }

      const box = document.getElementById("formErrors");
      if (errors.length) {
        e.preventDefault();
        if (box) {
          box.innerHTML = "<ul><li>" + errors.join("</li><li>") + "</li></ul>";
          box.style.display = "block";
        } else {
          alert(errors.join("\n"));
        }
      }
    });
  }
})();
