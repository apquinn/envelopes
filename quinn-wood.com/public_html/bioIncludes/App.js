const slider = document.querySelector(".slider");

const leftArrow = document.querySelector(".left");
const rightArrow = document.querySelector(".right");
const indicatorParents = document.querySelector(".controls ul");
var sectionIndex = 0;

function setIndex() {
  document.querySelector(".controls .selected").classList.remove("selected");
  slider.style.transform = "translate(" + sectionIndex * -50 + "%)";
}

document.querySelectorAll(".controls li").forEach(function (indicator, ind) {
  indicator.addEventListener("click", function () {
    sectionIndex = ind;
    setIndex();
    indicator.classList.add("selected");
  });
});

leftArrow.addEventListener("click", function () {
  sectionIndex = sectionIndex > 0 ? sectionIndex - 1 : 0;
  setIndex();
  indicatorParents.children[sectionIndex].classList.add("selected");
});

rightArrow.addEventListener("click", function () {
  sectionIndex = sectionIndex < 1 ? sectionIndex + 1 : 1;
  setIndex();
  indicatorParents.children[sectionIndex].classList.add("hidden");
});

/* SECOND SLIDER */
const slider2 = document.querySelector(".slider2");

const leftArrow2 = document.querySelector(".left2");
const rightArrow2 = document.querySelector(".right2");
const indicatorParents2 = document.querySelector(".controls2 ul");
var sectionIndex2 = 0;

function setIndex2() {
  document.querySelector(".controls2 .selected").classList.remove("selected");
  slider2.style.transform = "translate(" + sectionIndex2 * -5 + "%)";
}

document.querySelectorAll(".controls2 li").forEach(function (indicator2, ind2) {
  indicator2.addEventListener("click", function () {
    console.log("HERE ");
    sectionIndex2 = ind2;
    setIndex2();
    indicator2.classList.add("selected");
  });
});

leftArrow2.addEventListener("click", function () {
  sectionIndex2 = sectionIndex2 > 0 ? sectionIndex2 - 1 : 0;
  setIndex2();
  indicatorParents2.children[sectionIndex2].classList.add("selected");
});

rightArrow2.addEventListener("click", function () {
  sectionIndex2 = sectionIndex2 < 1 ? sectionIndex2 + 1 : 1;
  setIndex2();
  indicatorParents2.children[sectionIndex2].classList.add("hidden");
});

/* MORE PICTURE LINK */
/*
document.querySelectorAll(".more-link").forEach(function (link, ind) {
  link.addEventListener("click", function () {
    document
      .querySelectorAll(".hidden-experience")
      [ind].classList.remove("hidden");
    document.querySelectorAll(".more-div")[ind].classList.add("hidden");
  });
});
*/

/**************** PROJECTS SLIDESHOWS **************** */
function InitiateSlideShow(dirName) {
  slideIndex = 1;
  document
    .getElementById("slideshow-container-" + dirName)
    .classList.remove("hidden");
  document
    .getElementById("slideshow-dots-" + dirName)
    .classList.remove("hidden");

  showSlides(slideIndex, dirName);
}

function plusSlides(n, dirName) {
  showSlides((slideIndex += n), dirName);
}

function currentSlide(n, dirName) {
  showSlides((slideIndex = n), dirName);
}

function showSlides(n, dirName) {
  let i;
  let slides = document.getElementsByClassName("mySlides-" + dirName);
  let dots = document.getElementsByClassName("dot-" + dirName);
  if (n > slides.length) {
    slideIndex = 1;
  }
  if (n < 1) {
    slideIndex = slides.length;
  }
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  slides[slideIndex - 1].style.display = "block";
  dots[slideIndex - 1].className += " active";
}
