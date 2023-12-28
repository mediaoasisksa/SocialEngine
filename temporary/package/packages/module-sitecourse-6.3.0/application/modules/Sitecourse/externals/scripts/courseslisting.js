const courseHtml = {
  /** @param {int} course average rating */
  rating: function (avgRating) {
    let html = "";
    for (let i = 1; i <= Math.min(Math.floor(avgRating), 5); ++i) {
      html += '<span class="browse__course__rating__full"></span>';
    }
    let decimal = avgRating - Math.min(Math.floor(avgRating), 5);
    if (decimal > 0 && avgRating <= 5) {
      html += '<span class="browse__course__rating__half"></span>';
    }
    let num = Math.floor(5 - avgRating);
    if (num < 0) num = 0;
    for (let i = 1; i <= num; ++i) {
      html += '<span class="no_ratings"></span>';
    }

    return html;
  },

  /** @param {string} course image path */
  image: function (course, type) {
    let html2 = "";
    let src =
      en4.core.staticBaseUrl +
      "application/modules/Sitecourse/externals/images/default_course_profile.png";
    if (course["img_src"]) src = course["img_src"];
    if (type != "my-courses") {
      html2 += `<button data-inset='false' data-mini='true' data-corners='false' 
      data-shadow='true' class="browse_share_content" 
      onclick = "Smoothbox.open('${course["share_url"]}')">
      <i class="fas fa-share-alt"></i>
      Share
      </button>`;
    }
    return `
    <span class="browse__course__image">
    <img src="${src}" alt="profile image">
    ${html2}
    </span>
    `;
  },

  topics: function (course) {
    let html = "";

    if (!course["topics"] || !course["topics"].length) {
      return html;
    }
    course["topics"].slice(0, 4).forEach((topic) => {
      if (topic["title"].length > 15) {
        html += `<li>${topic["title"].substring(0, 15)}...</li>`;
      } else {
        html += `<li>${topic["title"]}</li>`;
      }
    });
    return html;
  },

  /** @param {string} percentage | type */
  progress: function (percentage = "8%", type) {
    // show percentage only at purchased courses widget
    if (type !== "purchased-courses") return "";
    return `
    <span class="browse__course__progress">
   <span class="browse__course__progress__bar">
   <span class="browse__course__progress__bar__completed" style="width: ${percentage}" ></span>
   </span>
   <span class="browse__course__progress__bar__number">
   ${percentage} completed
   </span>
   </span>
   `;
  },
  /** @param {type} widget type */
  enrollBtn: function (course, params) {
    let html = "";
    if (course["is_favourite"]) {
      html += `<button class="browse__course__widhlist__added" title="Make Favourite" onclick="toggleFavourite('${course["favourite_url"]}','${course["course_id"]}','${params["course_container_id"]}')">
    <i class="far fa-heart" ></i></button>`;
    } else {
      html += `<button class="browse__course__widhlist" title="Make Favourite" onclick="toggleFavourite('${course["favourite_url"]}','${course["course_id"]}','${params["course_container_id"]}')">
    <i class="far fa-heart" ></i></button>`;
    }
    let html2 = "";
    if (course["canBuy"]) {
      html2 += `<form id='purchase_course_form' class='global_form_box' method='post' action='${course["getEnrolled"]}'>
    <input type="hidden" value="${course["course_id"]}" name="course" />
    <input type="submit" value="GET ENROLLED">
    </form>`;
    } else if (course["isPurchased"]) {
      html2 = `<a href='${course["getEnrolled"]}'>GO TO COURSE</a>`;
    } else {
      html2 = `${course["getEnrolled"]}`;
    }
    if (params["type"] !== "browse-courses") return "";
    return `
  <span class="browse__course__enrolled btn" id="enroll_btn_${params["course_container_id"]}_${course["course_id"]}">
  ${html2}
  ${html}
  </span>
  `;
  },
  /** @param {string} difficulty name | category name */
  catDiff: function (difName, catName) {
    if (catName.length > 14) {
      catName = catName.substring(0, 14).trim() + "...";
    }

    return ` 
  <span class="browse__course__difficulty_cat">
  <span class="browse__course__difficulty" title="Difficulty Level">
  <i class="fas fa-level-up-alt"></i>
  <p>${difName}</p>
  </span>
  <span class="browse__course__category" title="Category">
  <i class="fas fa-list-ul"></i>
  <p>${catName}</p>
  </span>
  </span>
  `;
  },

  editOptions: function (course, type) {
    if (type != "my-courses") return "";
    let deleteHtml = ``;
    if (course["delete_permission"]) {
      deleteHtml += `
    <li onclick = "Smoothbox.open('${course["delete_url"]}')"><i class="fas fa-trash-alt"></i>Delete</li>
    `;
    }
    return `<span class="browse__course__pull__down">
  <i class="fas fa-ellipsis-v"></i>
  <ul class="browse__course__pull__down__content">
  <li><a href="${course["edit_url"]}"><i class="fas fa-edit"></i>Edit</a></li>
  <li onclick = "Smoothbox.open('${course["details_url"]}')"><i class="fas fa-info-circle"></i>Details</li>
  ${deleteHtml}
  </ul>
  </span>`;
  },

  toolTip: function (course, type) {
    let html = "";
    let newest = "";
    let toprated = "";
    let bestseller = "";
    let overview = "";
    if (course["newest"] || type == "newest_courses") {
      newest += `<span class="browse_course_tooltip_sposored">Newest</span>`;
    }
    if (course["toprated"]) {
      toprated += `<span class="browse_course_tooltip_toprated">Top-Rated</span>`;
    }
    if (course["bestseller"] == 1) {
      bestseller += `<span class="browse_course_tooltip_bestseller">Best-Seller</span>`;
    }
    if (course["overview"] != "") {
      overview += `${course["overview"].substring(0, 60)}...`;
    }
    if (type != "carousel_courses") {
      html += `<div class="browse_course_tooltip">
  <span class="course_title_heading">
  <h2>${course["link"]}</h2>
  <span class="course__updated">
  <p>Last Modified ${course["modified_date"]}</p>
  </span>
  </span>
  <span class="browse_course_tooltip_tags">
  ${bestseller}
  ${toprated}
  ${newest}
  </span>
  <span class="browse_course_desc">
  <p>${overview}</p>
  <h4>Topics</h4>
  <ul class="browse_course_desc_list">
  ${courseHtml.topics(course)}</ul>
  </span>
  </div>`;
    }
    return html;
  },
};

/**
 *
 * @param {array} Model_Course objects array
 * @param {object} params
 * @return {string} html string to render the courses
 *
 */
function getCoursesHtml(courses, params) {
  let html = "";
  courses.forEach((course) => {
    course["price"] = course["price"]
      ? `${params["currency"]}&nbsp;${course["price"]}`
      : "Free";
    course["favourite_url"] = course["favourite_url"] ?? "#";
    const favIconHtml =
      course["is_favourite"] && params["type"] != "my-courses"
        ? '<i class="fas fa-heart"></i>'
        : "";
    const memberTxt = course["buyers_count"] <= 1 ? "member" : "members";
    html += `
    <li class="browse__course__block" id='${course["course_id"]}' >
    ${courseHtml.image(course, params["type"])}
    <!-- editor options-->
    ${courseHtml.editOptions(course, params["type"])}

    <span class="browse__course__wishlist__indication" id="wishlist_${
      params["course_container_id"]
    }_${course["course_id"]}">
    ${favIconHtml}
    </span>
    <span class="browse__course__info">
    <span class="browse__course__title">
    <h2>${course["link"]}</h2>
    <span class="browse__course__created">
    Created by ${course["owner_name"]}
    </span>
    </span>

    ${courseHtml.catDiff(course["difficulty_name"], course["category_name"])}

    <span class="browse__course__members__duration">
    <span class="browse__course__members" title="Members Enrolled">
    <i class="fas fa-user"></i>
    <p>${course["buyers_count"]}&nbsp;${memberTxt}</p>
    </span>
    <span class="browse__course__duration" title="Duration">
    <i class="fas fa-clock"></i>
    <p>${course["duration"]}&nbsp;Hrs</p>
    </span>
    </span>

    ${courseHtml.progress(course["percentage"], params["type"])}

    <span class="browse__course__rating__price">
    <span class="browse__course__ratings">
    ${courseHtml.rating(course["rating"])}
    </span>
    <span class="browse__course__price">
    ${course["price"]}
    </span>
    </span>
    ${courseHtml.enrollBtn(course, params)}
    </span>
    ${courseHtml.toolTip(course, params["type"])}
    </li>
    `;
  });
  return html;
}

/**
 *
 * @param {boolean} loadMore
 * @param {int} coursesCount
 * @param {object} params
 *
 */
function toggleItemsDisplay(loadMore, coursesCount, params) {
  const tipElem = document.querySelector(params["tip_container"]);
  const moreItemsElem = document.getElementById(params["load_more_item_id"]);

  // toggle load more elem
  if (moreItemsElem) {
    moreItemsElem.style.display = loadMore ? "block" : "none";
  }
  // hide tip elem
  if (tipElem && coursesCount) {
    tipElem.style.display = "none";
  }
}

/**
 *
 * @param {object} params object
 * send the ajax request and handle response
 *
 */
function loadMoreItems(params) {
  if (!params && Object.keys(params).length === 0) {
    return;
  }
  const loadingEl = document.getElementById("loding_image_view");
  if (loadingEl) {
    loadingEl.style.display = "block";
    document.getElementById("load_more_button").style.display = "none";
  }
  // ajax request
  scriptJquery.ajax({
    url: params["url"],
    type: "post",
    data: {
      format: "json",
      is_ajax: true,
      cache: false,
      page_id: params["page_id"],
      textTrucationLimit: params["textTrucationLimit"],
      item_count: params["item_count"],
      pagination_params: params["pagination_params"],
    },
    success: function (responseJSON) {
      if (loadingEl) {
        loadingEl.style.display = "none";
        document.getElementById("load_more_button").style.display = "block";
      }
      // user is not authorized to send the request
      if (responseJSON.status === 401) {
        return;
      }
      let courses = responseJSON.courses;
      // courses are not available
      if (!courses || !courses.length) {
        return;
      }

      toggleItemsDisplay(responseJSON.load_more, courses.length, params);
      params["currency"] = responseJSON.currency ?? "USD";
      const coursesItemsElem = document.getElementById(
        params["course_container_id"]
      );
      if (coursesItemsElem) {
        coursesItemsElem.insertAdjacentHTML(
          "beforeend",
          getCoursesHtml(courses, params)
        );
        showSpecificFields(params["course_fields"], coursesItemsElem.id);
      }
      params["page_id"] += 1;

      // pull down options click handle
      const pullDownOptEl = document.querySelectorAll(
        ".browse__course__pull__down"
      );

      handlePullDownOptions(pullDownOptEl, params["type"]);
    },
  });
}

/**
 *
 * @param {array} course fields need to display
 * @param {parentId} parent id where the courses are attached
 *
 */
function showSpecificFields(fields, parentId) {
  if (!parentId) return;

  const diffEl = document
    .getElementById(parentId)
    .querySelectorAll(".browse__course__difficulty");
  const catEl = document
    .getElementById(parentId)
    .querySelectorAll(".browse__course__category");
  const creatorEl = document
    .getElementById(parentId)
    .querySelectorAll(".browse__course__created");
  const memeberEl = document
    .getElementById(parentId)
    .querySelectorAll(".browse__course__members");
  // check the fields needs not to include
  if (!fields || !fields.includes("postedBy")) {
    creatorEl?.forEach((elem) => elem.remove());
  }
  if (!fields || !fields.includes("enrolledCount")) {
    memeberEl?.forEach((elem) => elem.remove());
  }
  if (!fields || !fields.includes("category")) {
    catEl?.forEach((elem) => elem.remove());
  }
  if (!fields || !fields.includes("difficultyLevel")) {
    diffEl?.forEach((elem) => elem.remove());
  }
}

/**
 *
 * @param {HTML_ELEMENT_ARRAY} pull down options array
 * @param {string} widget name
 *
 */
function handlePullDownOptions(optionEls, widgetType) {
  if (!optionEls || widgetType !== "my-courses") return;

  optionEls.forEach((elem) => {
    elem.addEventListener("mouseover", function () {
      event.stopPropagation();
      elem.childNodes[3].style.display = "block";
    });
  });

  let courseEls = document.querySelectorAll(".browse__course__info");
  courseEls.forEach((elem, idx) => {
    elem.addEventListener("mouseover", function () {
      optionEls[idx].childNodes[3].style.display = "none";
    });
  });
}

/**
 * @param {string} url where to send the ajax request
 * @param {html-element} element whose text to toggle
 */
function toggleFavourite(url = "", id, containerId) {
  // no url is passed
  if (!url) return;
  scriptJquery.ajax({
    url: url,
    data: {
      format: "json",
    },
    success: function (responseJSON) {
      const favClass = containerId + "_" + id;
      const element = document.getElementById("wishlist_" + favClass);
      element.innerHTML = responseJSON.favourite
        ? '<i class="fas fa-heart"></i>'
        : "";
      const notLiked = document
        .getElementById("enroll_btn_" + favClass)
        .querySelector(`.browse__course__widhlist`);
      const liked = document
        .getElementById("enroll_btn_" + favClass)
        .querySelector(`.browse__course__widhlist__added`);
      if (liked != null) {
        liked.className = "browse__course__widhlist";
      } else if (notLiked != null) {
        notLiked.className = "browse__course__widhlist__added";
      }
    },
  });
}
