var checkDuplicateEmail = function () {
  var email = document.getElementById("email").value;
  var url = "http://localhost/ecommerce-goup3/api/users.php?email=" + email;

  httpGetAsync(url, function (responseText) {
    var data = JSON.parse(responseText);
    var status = data.status;

    var errorField = document.getElementById("email_error");

    if (status === "duplicate") {
      errorField.textContent =
        "The email is already registered. Try another one.";
    } else {
      errorField.textContent = "";
    }
  });
};

function httpGetAsync(url, callback) {
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      callback(xmlHttp.response);
    }
  };
  xmlHttp.open("GET", url, true);
  xmlHttp.send(null);
}
