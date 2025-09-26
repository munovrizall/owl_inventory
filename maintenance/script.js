document.querySelectorAll("tbody tr").forEach((row) => {
  const detailId = row.getAttribute("data-detail-id"),
    circles = row.querySelectorAll(".circle"),
    progressBar = row.querySelector(".indicator"),
    buttons = row.querySelectorAll("button"),
    noSoInput = row.querySelector(".no-so"),
    noResiInput = row.querySelector(".no-resi");

  let currentStep = parseInt(row.querySelector("input[name='status[" + detailId + "]']").value);
  let timeoutId = null;
  let beforeUnloadSent = false; // Flag untuk cek apakah sendBeacon sudah dikirim
  let lastSavedStep = currentStep;
  let lastSavedNoSo = noSoInput.value;
  let lastSavedNoResi = noResiInput.value;
  let so = parseInt(row.querySelector("input[name='so[" + detailId + "]']").value);

  // Fungsi untuk update UI
  function updateUI() {
    circles.forEach((circle, index) => {
      circle.classList[index < currentStep ? "add" : "remove"]("active");
    });

    if (so === 0) {
      circles[2].classList.remove("active");
    }
    progressBar.style.width = `${((currentStep - 1) / (circles.length - 1)) * 100}%`;

    buttons[1].disabled = currentStep === circles.length;
    buttons[0].disabled = currentStep === 1;
  }

  // Fungsi untuk kirim AJAX
  function updateStatus() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      if (currentStep !== lastSavedStep) { // Cek kalau status beneran berubah
        $.ajax({
            url: 'monitoring_detail.php',
            method: 'POST',
            data: {
                update_status: true,
                detail_id: detailId,
                status: currentStep
            },
            success: function(response) {
                console.log("Status berhasil diperbarui:", response);
                lastSavedStep = currentStep; // Update last saved step biar nggak double
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
      }
    }, 2000);
  }

  // Fungsi untuk update langkah
  const updateSteps = (e) => {
    let newStep = e.target.id === "next" ? currentStep + 1 : currentStep - 1;

    if (newStep < 1) {
      newStep = 1;
    } else if (newStep > circles.length) {
      newStep = circles.length;
    }

    if (newStep !== lastSavedStep) {
      currentStep = newStep;
      row.querySelector("input[name='status[" + detailId + "]']").value = currentStep;
      updateUI();
      updateStatus();
    }
  };

  // Fungsi untuk update nomor SO TextField
  function updateTextField(fieldName, value) {
    $.ajax({
      url: 'monitoring_detail.php',
      method: 'POST',
      data: {
        update_text: true,
        detail_id: detailId,
        field: fieldName,
        value: value
      },
      success: function (response) {
        console.log(`Field ${fieldName} berhasil diperbarui:`, response);
        if (fieldName === "no_so") lastSavedNoSo = value;
        if (fieldName === "no_resi") lastSavedNoResi = value;
      },
      error: function (xhr, status, error) {
        console.error(`AJAX error saat update ${fieldName}:`, error);
      }
    });
  }

  // Cegah listener ganda dengan .off() lalu .on()
  $(buttons).off("click").on("click", updateSteps);

  // Handle sebelum halaman ditutup
  window.addEventListener("beforeunload", function () {
    if (!beforeUnloadSent && timeoutId && currentStep !== lastSavedStep) {
      beforeUnloadSent = true; // Set flag biar ga kirim ulang
      clearTimeout(timeoutId);

      let data = new URLSearchParams();
      data.append("update_status", "true");
      data.append("detail_id", detailId);
      data.append("status", currentStep);

      navigator.sendBeacon("monitoring_detail.php", data);
    }

    if (noSoInput.value !== lastSavedNoSo) {
      updateTextField("no_so", noSoInput.value);
    }

    if (noResiInput.value !== lastSavedNoResi) {
        updateTextField("no_resi", noResiInput.value);
    }
  });

  // Event listener untuk update nomor SO dan nomor resi
  noSoInput.addEventListener("blur", function () {
    if (noSoInput.value !== lastSavedNoSo) {
      updateTextField("no_so", noSoInput.value);
    }
  });

  noResiInput.addEventListener("blur", function () {
    if (noResiInput.value !== lastSavedNoResi) {
      updateTextField("no_resi", noResiInput.value);
    }
  });

  updateUI();
});
