import $ from 'jquery';

class Search {
  /* Properties */
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = $('#search-overlay__result');
    this.openButton = $('.js-search-trigger');
    this.closeIcon = $('.search-overlay__close');
    this.inputSearch = $('.search-term');
    this.searchOverlay = $('.search-overlay');
    this.events();
    this.isOverlayOpen = false;
    this.typingTimer;
    this.isSpinnerVisible = false;
    this.perviousValue;
  }

  /* Events */
  events() {
    this.openButton.on('click', this.openOverlay.bind(this));
    this.closeIcon.on('click', this.closeOverlay.bind(this));
    this.inputSearch.on('keyup', this.sendInput.bind(this)); // keydown event is not good because it fires up before the browser change the value of the input if the user presses the key.
    $(document).on('keydown', this.keyPressDispatcher.bind(this));
  }
  /* Methods and Functions */
  sendInput() {
    if (this.inputSearch.val() != this.perviousValue) {
      clearTimeout(this.typingTimer);

      if (this.inputSearch.val()) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);
      } else {
        clearTimeout(this.typingTimer);
        this.resultsDiv.html('');
        this.isSpinnerVisible = false;
      }
    }

    this.perviousValue = this.inputSearch.val();
  }

  getResults() {
    $.when(
      $.getJSON(
        universityData.root_url +
          '/wp-json/wp/v2/posts?search=' +
          this.inputSearch.val()
      ),
      $.getJSON(
        universityData.root_url +
          '/wp-json/wp/v2/pages?search=' +
          this.inputSearch.val()
      )
    ).then(
      (posts, pages) => {
        let combinedResults = posts[0].concat(pages[0]);
        this.resultsDiv.html(`
    <h2 class="search-overlay__section-title">General Information</h2>
    ${
      combinedResults.length
        ? `<ul class="link-list min-list">`
        : `<p>No search results were found.</p>`
    }
    ${combinedResults
      .map(
        (post) =>
          `<li><a href="${post.link}">${post.title.rendered}</a> ${
            post.type == 'post' ? `By ` + post.authorName : ''
          }</li>`
      )
      .join('')}
      ${combinedResults.length ? `</ul>` : ''}
      `);
        this.isSpinnerVisible = false;
      },
      (err) => {
        this.resultsDiv.html(`<p>Unexpected error! Please try again</p>`);
      }
    );
  }

  keyPressDispatcher(e) {
    if (
      e.keyCode == 83 &&
      !this.isOverlayOpen &&
      !$('input, textarea').is(':focus') // any other input or textarea is focused don't trigger the searchOverlay
    ) {
      this.openOverlay();
    }

    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  openOverlay() {
    console.log('I have been clicked');
    this.searchOverlay.addClass('search-overlay--active');
    this.inputSearch.val('');
    this.resultsDiv.html('');
    setTimeout(() => this.inputSearch.focus(), 301);
    $('body').addClass('body-no-scroll');
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass('search-overlay--active');
    this.isOverlayOpen = false;
  }

  addSearchHTML() {
    $('body').append(`
      <div class="search-overlay">
        <div class="search-overlay__top">
            <div class="container">
                <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                <input class="search-term" type="text" name="search-term" id="search-term" placeholder="What are you looking for?">
                <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
            </div>
        </div>
        <div class="container">
            <div id="search-overlay__result">
            </div>
        </div>
      </div>
      `);
  }
}

export default Search;
