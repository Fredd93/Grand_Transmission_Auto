<?php
// Ensure $activePage is set; if not, default to empty string.
if (!isset($activePage)) {
    $activePage = '';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
  <div class="container">
    <a class="navbar-brand" href="/Home">
      <img src="/assets/images/grand_transmission_auto_website Image.png" alt="Logo" style="height:80px;">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item <?php echo ($activePage == 'Home') ? 'active' : ''; ?>">
          <a class="nav-link" href="/Home">Home</a>
        </li>
        <li class="nav-item <?php echo ($activePage == 'cars') ? 'active' : ''; ?>">
          <a class="nav-link" href="/cars">Cars</a>
        </li>
        <li class="nav-item <?php echo ($activePage == 'contact') ? 'active' : ''; ?>">
          <a class="nav-link" href="/contact.php">Contact Us</a>
        </li>
        <?php
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'manager'])) {
            echo '<li class="nav-item ' . (($activePage=='orders') ? 'active' : '') . '">
                    <a class="nav-link" href="/offers.php">Offers</a>
                  </li>';
        }
        ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo '<li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                  </li>';
        } else {
            echo '<li class="nav-item">
                    <a class="nav-link btn btn-primary text-white" href="/login">Employee Login</a>
                  </li>';
        }
        ?>
      </ul>
    </div>
  </div>
</nav>
