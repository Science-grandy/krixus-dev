let selectElement = document.getElementById("billingCycle");
if (selectElement) {
  selectElement.addEventListener("change", function() {
    let billingCycle = this.value;

    // Prices for each billing cycle
    const prices = {
      quarterly: ["₦40,000", "₦90,000", "₦120,000", "₦150,000"],
      "semi-annual": ["₦70,000", "₦170,000", "₦230,000", "₦290,000"],
      annual: ["₦105,000", "₦255,000", "₦345,000", "₦435,000"]
    };

    // Update the prices based on the selected billing cycle
    document.getElementById("price1").textContent = prices[billingCycle][0];
    document.getElementById("price2").textContent = prices[billingCycle][1];
    document.getElementById("price3").textContent = prices[billingCycle][2];
    document.getElementById("price4").textContent = prices[billingCycle][3];
  });
}

// Add a confetti to the website
window.onload = function() {
  confetti({
    particleCount: 500,
    angle: 90,
    spread: 360,
    startVelocity: 30,
    origin: { x: 0.5, y: 0 },
    colors: ['#bb0000', '#ffffff', '#0000bb'],
    disableForReducedMotion: true
  });
};

document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    confetti.reset(); // Stops and clears confetti
  }
});

// Close alert boxes
$(document).ready(function() {
  setTimeout(function() {
    $(".msgAlert").addClass("fade-out-up");
    setTimeout(function() {
      $(".msgAlert").alert('close');
        }, 500);
    }, 3000);

  $('#demoRequestForm').on('submit', function (e) {
    e.preventDefault();
    // Collect form data
    var formData = $(this).serialize();
    $('#scheduleBtn').text('Scheduling...');
    
    // Submit form via AJAX
    $.ajax({
      type: 'POST',
      url: 'sendmail',
      data: formData,
      success: function (response) {
        try {
          var res = JSON.parse(response);
          if (res.status == 1) {
            $('#responseMessage').html('<div class="alert alert-secondary text-center alert-dismissible fade show mt-3 msgAlert" role="alert">' + res.message + '</div>');
            $('#scheduleBtn').text('Schedule now');
            $('#demoRequestForm')[0].reset();
          } else {
            $('#responseMessage').html('<div class="alert alert-secondary text-center alert-dismissible fade show mt-3 msgAlert" role="alert">' + res.message + '</div>');
          }

          // Optionally scroll to the demo request form after submission
          $('html, body').animate({
            scrollTop: $('#demoRequestForm').offset().top
          }, 1000);
          setTimeout(function() {
            $(".msgAlert").addClass("fade-out-up");
            setTimeout(function() {
              $(".msgAlert").alert('close');
            }, 500);
          }, 3000);
        } catch (e) {
          $('#responseMessage').html('<div class="error">Error processing the request.</div>');
        }
      },
      error: function () {
        $('#responseMessage').html('<div class="error">Failed to submit the form. Please try again.</div>');
      }
    });
  });

  async function hashPrice(price) {
    const encoder = new TextEncoder();
    const data = encoder.encode(price);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
  }

  $('.selectPlan').click(async function(e) {
      e.preventDefault();

      var price = $(this).siblings('.plan-price').text();
      var opt = $(this).data('opt');
      var duration = $('#billingCycle').val();

      var hashedPrice = await hashPrice(price);
      window.location.href = 'checkout?opt=' + opt + '&p=' + hashedPrice + '&d=' + duration;
  });
});

// Paystack Popup
// const popup = new PaystackPop();
// popup.resumeTransaction(access_code);