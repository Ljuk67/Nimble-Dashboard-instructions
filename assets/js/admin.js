( function() {
	'use strict';

	var config = window.nimbleDashboardInstructions;

	if ( ! config || ! config.mediaNoticeBeforeLink || ! config.mediaNoticeAfterLink || ! config.tinypngUrl || ! config.tinypngLabel ) {
		return;
	}

	var noticeClass = 'nimble-dashboard-instructions-modal-notice';
	var noticeAttr = 'data-nimble-dashboard-instructions-notice';
	var uploaderSelectors = [
		'.uploader-inline-content .upload-ui',
		'.media-frame-content .upload-ui',
		'.upload-ui'
	];

	function buildNotice() {
		var notice = document.createElement( 'div' );
		var text = document.createElement( 'p' );
		var strong = document.createElement( 'strong' );
		var link = document.createElement( 'a' );

		notice.className = noticeClass;
		notice.setAttribute( noticeAttr, 'true' );

		strong.textContent = 'Important: ';
		link.href = config.tinypngUrl;
		link.target = '_blank';
		link.rel = 'noopener noreferrer';
		link.textContent = config.tinypngLabel;

		text.appendChild( strong );
		text.appendChild( document.createTextNode( config.mediaNoticeBeforeLink ) );
		text.appendChild( link );
		text.appendChild( document.createTextNode( config.mediaNoticeAfterLink ) );
		notice.appendChild( text );

		return notice;
	}

	function findUploadUi( container ) {
		var index;
		var uploadUi;

		if ( ! container ) {
			return null;
		}

		for ( index = 0; index < uploaderSelectors.length; index++ ) {
			uploadUi = container.querySelector( uploaderSelectors[ index ] );

			if ( uploadUi ) {
				return uploadUi;
			}
		}

		return null;
	}

	function insertNotice( container ) {
		var uploadUi;
		var notice;

		if ( ! container ) {
			return;
		}

		uploadUi = findUploadUi( container );
		notice = container.querySelector( '[' + noticeAttr + ']' );

		if ( ! notice ) {
			notice = buildNotice();
		}

		if ( ! uploadUi ) {
			return;
		}

		if ( notice.parentNode !== uploadUi ) {
			uploadUi.insertBefore( notice, uploadUi.firstChild );
		}
	}

	function renderMediaNotices() {
		document.querySelectorAll( '.media-modal .media-modal-content' ).forEach( insertNotice );
		document.querySelectorAll( '.media-frame-content' ).forEach( insertNotice );
	}

	document.addEventListener( 'DOMContentLoaded', renderMediaNotices );

	var observer = new MutationObserver( function() {
		renderMediaNotices();
	} );

	observer.observe( document.body, {
		childList: true,
		subtree: true
	} );
}() );
