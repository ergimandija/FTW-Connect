<!DOCTYPE html>
<html lang="en">

<?php include '../src/includes/header.php'; ?>

<body>

    <main>

        <!-- ABOUT HERO -->
        <section class="container pt-4 pb-2 text-center">

            <h1 class="display-4 fw-bold mb-4">
                ABOUT FTW CONNECT
            </h1>

            <p class="lead mb-0 mx-auto" style="max-width: 800px;">
                FTW Connect is a student-focused platform designed to improve communication,
                collaboration and engagement within the university community.
            </p>

        </section>

        <!-- MISSION -->
        <section class="container py-2">

            <div class="rounded-4 p-4 text-center about-box">

                <h2 class="fw-bold mb-3">
                    OUR MISSION
                </h2>

                <p class="lead mb-0 mx-auto" style="max-width: 900px;">
                    University life is about more than attending lectures. <br>
                    FTW Connect helps students connect, exchange ideas,
                    organize study groups and stay informed about campus activities.
                </p>

            </div>

        </section>

        <!-- WHAT WE OFFER -->
        <section class="container pt-4 pb-5">

            <div class="text-center mb-4">

                <h2 class="fw-bold mb-3">
                    WHAT WE OFFER
                </h2>

                <p class="lead">
                    Discover the main features of FTW Connect.
                </p>

            </div>

            <!-- FEATURE CARDS -->
            <div class="row g-4">

                <!-- CARD 1 -->
                <div class="col-md-4">

                    <div class="flip-card" onclick="flipCard(this)">

                        <div class="flip-card-inner">

                            <div class="flip-card-front shadow-sm">
                                <h3 class="h4 mb-3">
                                    Messaging
                                </h3>

                                <p>
                                    Click to learn more
                                </p>
                            </div>

                            <div class="flip-card-back shadow-sm">
                                <h3 class="h4 mb-3">
                                    Messaging
                                </h3>

                                <p>
                                    Chat with classmates and stay connected through direct conversations directly via your student Email!
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 2 -->
                <div class="col-md-4">

                    <div class="flip-card" onclick="flipCard(this)">

                        <div class="flip-card-inner">

                            <div class="flip-card-front shadow-sm">
                                <h3 class="h4 mb-3">
                                    Groups
                                </h3>

                                <p>
                                    Click to learn more
                                </p>
                            </div>

                            <div class="flip-card-back shadow-sm">
                                <h3 class="h4 mb-3">
                                    Groups
                                </h3>

                                <p>
                                    Create study groups, project teams and communities based on shared interests. Stay in contact on one shared platform!
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 3 -->
                <div class="col-md-4">

                    <div class="flip-card" onclick="flipCard(this)">

                        <div class="flip-card-inner">

                            <div class="flip-card-front shadow-sm">
                                <h3 class="h4 mb-3">
                                    Campus Feed
                                </h3>

                                <p>
                                    Click to learn more
                                </p>
                            </div>

                            <div class="flip-card-back shadow-sm">
                                <h3 class="h4 mb-3">
                                    Campus Feed
                                </h3>

                                <p>
                                    Stay updated with announcements, posts and activities happening on campus. No more switching between different platforms! Everything you need on one page.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- TEAM -->
        <section class="container pt-4 pb-5">

            <div class="text-center mb-4">

                <h2 class="fw-bold mb-3">
                    WHO WE ARE
                </h2>

                <p class="lead">
                    We are a group of five students from FH Technikum Wien who built this website together as a student project.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md">
                    <div class="card h-100 border-0 shadow-sm team-card">
                        <h3 class="h5">David Berkovics</h3>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card h-100 border-0 shadow-sm team-card">
                        <h3 class="h5">Ergi Mandija</h3>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card h-100 border-0 shadow-sm team-card">
                        <h3 class="h5">Leonie Mitterhauser</h3>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card h-100 border-0 shadow-sm team-card">
                        <h3 class="h5">Adrian Pillar</h3>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card h-100 border-0 shadow-sm team-card">
                        <h3 class="h5">Frank Voithofer</h3>
                    </div>
                </div>

            </div>

        </section>

    </main>

    <?php include '../src/includes/footer.php'; ?>

    <script>
        function flipCard(card) {
            document.querySelectorAll('.flip-card').forEach(function (currentCard) {
                if (currentCard !== card) {
                    currentCard.classList.remove('flipped');
                }
            });

            card.classList.toggle('flipped');
        }
    </script>

</body>
</html>