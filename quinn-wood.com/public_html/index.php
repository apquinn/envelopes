<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="./bioIncludes/App.css?<?php echo time(); ?>" />
		<meta name="description" content="Quinnwood is a custom furniture design business tucked away in Michigan's upper peninsula. It is owned by craftsman Drew Quinn.">
		<meta name="keywords" content="woodworking, custom furniture, furniture design marquette" />
		<meta name="google-site-verification" 	content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
  <body>
    <div class="header-black-background">
      <img alt="sea quinn" src=./bioIncludes/pics/header-background.jpg class="responsive-img seaquinn"/>
      <div class="header-text-wrapper">
        <div class="header-text1">Quinn Wood</div>
        <h3>Woodworker. Furniture Designer. Groovy Individual.</h3>
      </div>
    </div>

    <div class="special-global-wrapper">
      <h1>What makes me so special?</h1>
      <div class="special-wrapper">
        <div class="special-section1">
          <p>
						Quinnwood is a custom furniture building business tucked away in
						Michigan's upper peninsula since 2008. After living the fast-paced life of a consultant and software
						architect through the booming of the Internet, I now live a quieter life building 
						furniture for discerning customers from a shop on 80 acres of forest. Life on the shores of Lake Superior 
						is unmatched with vast beaches in the summer and winters with over 200 inches of snow. The life of skiing and 
						snowshoeing in the winter and hiking and swimming in the summer are shared with my wife, Amy, and our two dogs, 
						Wondermutt the Australian cattle dog and Maisy the Rottweiler.
          </p>
          <h4>Why Quinn Wood?</h4>
          <p>
            Quinn Wood was the name of my grand father's boat. So many great memories that
            are now memorialized with my company name.
          </p>
        </div>

        <div class="special-section2">
          <img
            alt="Drew Quinn"
            class="responsive-img"
            src="./bioIncludes/pics/me.jpg"
          />
          <p>
            "The unchanged passion of my life has been to create beauty from God's natural resources."
          </p>
        </div>
      </div>
    </div>

    <div class="blue">
      <div class="special-global-wrapper">
        <div class="blueBackground">
          <div class="blue-wrapper bar">
            <div>
              <img alt="What do I do" src="./bioIncludes/pics/what.jpg" class="responsive-img"/>
            </div>
            <h4>What do I do?</h4>
            <p>
              I design and build custom furniture for clients all over the country.
            </p>
            <p><a href="#projects">See my projects</a></p>
          </div>
          <div class="blue-wrapper bar">
            <div>
              <img alt="My expertise" src="./bioIncludes/pics/my.jpg" class="responsive-img" />
            </div>
            <h4>My expertise</h4>
            <p>
              My Shop is a 900 sqr ft building deep in the woods of our 80 acres.
            </p>
            <p><a href="#shop">Check out my shop</a></p>
          </div>
          <div class="blue-wrapper">
            <div>
              <img alt="How can I help" src="./bioIncludes/pics/how.jpg" class="responsive-img" />
            </div>
            <h4>How can I help?</h4>
            <p>
              With 15 years of woodworking under my belt, I can design and build custom woodworking solutions for your home or office.
            </p>
            <p><a href="#help">Get in touch</a></p>
          </div>
        </div>
      </div>
    </div>

    <div class="blackBackground">
      <div class="special-global-wrapper">
        <div class="projects-title">What people are saying</div>
        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("What") ?>
          </article>
        </section>
			</div>
		</div>


    <div class="blue" id="projects">
      <div class="special-global-wrapper">
        <div class="projects-title">Things I've done</div>
        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Desk1") ?>
						<h4>Creative Writing Table</h4>
          </article>
        </section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("EndTable") ?>
            <h4>Walnut and Birdseye Maple End Table</h4>
          </article>
        </section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Nightstand1") ?>
            <h4>Curly Maple and Cherry Nightstand</h4>
          </article>
        </section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Nightstand2") ?>
						<h4>Curly Maple and Cherry Nightstand</h4>
					</article>
        </section>

        <section class="projects-section">
          <article class="project-box">
            <div class="img-alignment">
							<img alt="A Creative Plant Stand" 
							src="./bioIncludes/pics/projects/plantstand.jpg" />
						</div>
						<h4>A Creative Plant Stand</h4>
					</article>
        </section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Bench1") ?>
						<h4>Curly Cherry Bench</h4>
					</article>
				</section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Bed") ?>
						<h4>Curly Maple and Cherry Bed</h4>
					</article>
        </section>

        <section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Bench2") ?>
						<h4>Walnut and Curly Maple Bench</h4>
					</article>
        </section>

        <section class="projects-section">
          <article class="project-box">
            <div class="img-alignment">
							<img alt="Small Writing Table" src="./bioIncludes/pics/projects/desk2.jpg" />
						</div>
						<h4>Small Writing Table</h4>
					</article>
				</section>

				<section class="projects-section">
          <article class="project-box">
						<?php AddSlideShow("Kitchen") ?>
						<div class="project-box-inside">
							<h4>Complete Custom Kitchen</h4>
						</div>
					</article>
				</section>

				<section class="projects-section" id="shop">
          <article class="project-box">
						<?php AddSlideShow("Shop") ?>
						<div class="project-box-inside">
							<h4>My Shop</h4>
						</div>
					</article>
				</section>
			</div>
		</div>
    
		<div class="footer-black-background" id="help">
			<img alt="drew" src="./bioIncludes/pics/footer-background.jpg" class="responsive-img" />

			<div class="footer-wrapper">
				<div class="footer-text-seaquinn">Quinn Wood</div>

				<div id="contact" class="footer-text-contact">Get in touch</div>
				<div class="footer-contact-types">
					Drew Quinn<br/>
					335 Pelissier Lake Road Marquette, MI 49855<br/>
					906-235-6303<br/>
					apquinn@gmail.com<br/>
					sea-quinn.com
				</div>
				<div id="footer-contact-types-choice"></div>
			</div>
			<br><br><br><br>
			<br><br><br><br>
		</div>
	</body>
</html>

<?php

	function AddSlideShow($dir) {
		echo '<div id="slideshow-container-'.$dir.'" class="slideshow-container hidden">';

			if($dir != "What"){
				$files = scandir(__DIR__."/bioIncludes/pics/Completed/".$dir);
				foreach($files as $file){
					if($file[0] != "."){
						echo '<div class="mySlides-'.$dir.' mySlides fade"><img src="/bioIncludes/pics/Completed/'.$dir.'/'.$file.'"></div>';
					}
				}
				echo '<a class="prev" onclick="plusSlides(-1, \''.$dir.'\')">❮</a>';
				echo '<a class="next" onclick="plusSlides(1, \''.$dir.'\')">❯</a>';
			}
			else {
				echo '<div class="mySlides-'.$dir.' mySlides fade">
              <span class="people-title">A word from my mentor</span>

              <span class="people-text">
                <p>
                  "Blah blah blah Blah blah blah Blah blah blah"
                </p>
              </span>

              <span class="people-who">
								<p>
									Armin Gallonnek<br>
									Woodworker<br>
									<a href="https://www.thebluebook.com/iProView/1607831/northern-sun-woodworks/subcontractors/">Northern Sun Woodworking</a>
								</p>
							</span>
					</div>';
				echo '<div class="mySlides-'.$dir.' mySlides fade">
						<span class="people-title">A word from a client</span>
						<span class="people-text">
							<p>
								"WorBlah blah blah Blah blah blah Blah blah blah"
							</p>
						</span>
						<span class="people-who">
							<p>
								Eric Johnson<br />
								Customer<br />
							</p>
						</span>
					</div>';
				echo '<a class="prev" onclick="plusSlides(-1, \''.$dir.'\')">❮</a>';
				echo '<a class="next" onclick="plusSlides(1, \''.$dir.'\')">❯</a>';
			}
		echo '</div><br/>';

		echo '<div id="slideshow-dots-'.$dir.'" class="slideshow-dots hidden" style="text-align:center">';
			if($dir != "What"){
				$count = 1;
				foreach($files as $file){
					if($file[0] != "."){
						echo '<span class="dot-'.$dir.' dot" onclick="currentSlide('.$count.', \''.$dir.'\')"></span>'; 
						$count++;
					}
				}
			}
			else {
				echo '<span class="dot-'.$dir.' dot" onclick="currentSlide(1, \''.$dir.'\')"></span>'; 
				echo '<span class="dot-'.$dir.' dot" onclick="currentSlide(2, \''.$dir.'\')"></span>'; 
			}
				echo '</div>';
	}
?>




<script src="bioIncludes/App.js?<?php echo time(); ?>"></script>
<script>InitiateSlideShow("Desk1")</script>
<script>InitiateSlideShow("EndTable")</script>
<script>InitiateSlideShow("Nightstand1")</script>
<script>InitiateSlideShow("Nightstand2")</script>
<script>InitiateSlideShow("Bench1")</script>
<script>InitiateSlideShow("Bed")</script>
<script>InitiateSlideShow("Bench2")</script>
<script>InitiateSlideShow("Kitchen")</script>
<script>InitiateSlideShow("Shop")</script>
<script>InitiateSlideShow("What")</script>

  
