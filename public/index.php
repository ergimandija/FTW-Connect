<!DOCTYPE html>
<html lang="en">

<?php include '../src/includes/header.php'; ?>

<body>

    <main>

        <!-- HERO -->
        <section class="container pt-5 pb-4">

            <div class="row align-items-center g-5">

                <!-- LEFT -->
                <div class="col-lg-6">

                    <h1 class="display-4 fw-bold mb-4">
                        Don't just study. Connect!
                    </h1>

                    <p class="lead mb-4">
                        FTW Connect helps students communicate,
                        organize groups and stay connected on campus.
                    </p>

                    <div class="d-flex gap-3">

                        <a href="register.php" class="nav-item">
                            Get Started
                        </a>

                        <a href="login.php" class="nav-item">
                            Login
                        </a>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-6 text-center">

                    <img
                        src="assets/img/logo.png"
                        alt="FTW Connect Logo"
                        class="img-fluid"
                        style="max-width: 320px;"
                    >

                </div>

            </div>

        </section>

        <!-- FEATURES -->
        <section class="container pt-4 pb-4">

            <!-- SECTION TITLE -->
            <div class="text-center mb-4">

                <h2 class="fw-bold mb-3">
                    Why FTW Connect?
                </h2>

                <p class="lead">
                    Everything students need in one platform.
                </p>

            </div>

            <!-- FEATURE CARDS -->
            <div class="row g-4">

                <!-- CARD 1 -->
                <div class="col-md-4">

                    <a href="userchats.php" class="text-decoration-none text-dark d-block h-100">

                        <div class="card h-100 border-0 shadow-sm">

                            <div class="card-body text-center p-4">

                                <h3 class="h4 mb-3">
                                    Messaging
                                </h3>

                                <p>
                                    Chat with classmates and stay connected in real time.
                                </p>

                            </div>

                        </div>

                    </a>

                </div>

                <!-- CARD 2 -->
                <div class="col-md-4">

                    <a href="create_group.php" class="text-decoration-none text-dark d-block h-100">

                        <div class="card h-100 border-0 shadow-sm">

                            <div class="card-body text-center p-4">

                                <h3 class="h4 mb-3">
                                    Groups
                                </h3>

                                <p>
                                    Create study groups and collaborate together.
                                </p>

                            </div>

                        </div>

                    </a>

                </div>

                <!-- CARD 3 -->
                <div class="col-md-4">

                    <a href="feed.php" class="text-decoration-none text-dark d-block h-100">

                        <div class="card h-100 border-0 shadow-sm">

                            <div class="card-body text-center p-4">

                                <h3 class="h4 mb-3">
                                    Campus Feed
                                </h3>

                                <p>
                                    Stay updated with news, activities and events.
                                </p>

                            </div>

                        </div>

                    </a>

                </div>

            </div>

        </section>

        <!-- CTA SECTION -->
        <section class="container py-5">

            <div class="rounded-4 p-5 text-center"
                style="background-color: #4D8C5980;">

                <h2 class="fw-bold mb-3">
                    Ready to join FTW Connect?
                </h2>

                <p class="lead mb-4">
                    Create your account and connect with students today.
                </p>

                <a href="register.php" class="nav-item">
                    Create Account
                </a>

            </div>

        </section>

    </main>

    <?php include '../src/includes/footer.php'; ?>

</body>
</html>