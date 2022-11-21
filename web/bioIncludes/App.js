const slider = document.querySelector('.slider');

const leftArrow = document.querySelector('.left');
const rightArrow = document.querySelector('.right');
const indicatorParents = document.querySelector('.controls ul');
var sectionIndex = 0;

function setIndex() {
	document.querySelector('.controls .selected').classList.remove('selected');
	slider.style.transform = 'translate(' + (sectionIndex) * -50 + '%)';
}


document.querySelectorAll('.controls li').forEach(function(indicator, ind) {
	indicator.addEventListener('click', function() {
		sectionIndex = ind;
		setIndex();
		indicator.classList.add('selected');
	});
});


leftArrow.addEventListener('click', function () {
	sectionIndex = (sectionIndex > 0) ? sectionIndex - 1 : 0;
	setIndex();
	indicatorParents.children[sectionIndex].classList.add('selected');
});

rightArrow.addEventListener('click', function () {
	sectionIndex = (sectionIndex < 1) ? sectionIndex + 1 : 1;
	setIndex();
	indicatorParents.children[sectionIndex].classList.add('hidden');
});


document.querySelectorAll('.more-link').forEach(function(link, ind) {
	link.addEventListener('click', function() {
		document.querySelectorAll('.hidden-experience')[ind].classList.remove('hidden');
		document.querySelectorAll('.more-div')[ind].classList.add('hidden');
	});
});


const moreMoreLink = document.querySelector('.more-more-link');

moreMoreLink.addEventListener('click', function () {
		document.querySelectorAll('.projects-section').forEach(function(section, ind) {
			section.classList.remove('hidden');
		});
		document.querySelectorAll('.more-more-div')[0].classList.add('hidden')
});




