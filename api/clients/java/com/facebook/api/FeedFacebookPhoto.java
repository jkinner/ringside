package com.facebook.api;

import java.net.URL;

/**
 * A simple Pair consisting of a Facebook Photos photo ID for an image appearing in 
 * a newsfeed/minifeed story and the destination URL for a click
 * on that image.
 * 
 * @see IFacebookRestClient
 * @see IFacebookRestClient#photos_get
 * @see FacebookRestClient#handleFeedImages
 */
public class FeedFacebookPhoto extends Pair<Long, URL>
  implements IFeedImage {

  /**
   * Creates a linked Facebook Photos photo to appear in a user's newsfeed/minifeed.
   * 
   * @param photoId the photo ID of a Facebook photo to appear in a user's newsfeed/minifeed
   * @param link the URL to which the image should link
   * @see IFacebookRestClient#photos_get
   * @see FacebookRestClient#handleFeedImages
   */
  public FeedFacebookPhoto(Long photoId, URL link) {
    super(photoId, link);
    if (null == photoId || null == link) {
      throw new IllegalArgumentException("Both photoId and linkUrl should be provided");
    }
    if (0L >= photoId) {
      throw new IllegalArgumentException("photoId should be a Facebook Photos ID > 0");
    }
  }

  /**
   * @return the Facebook Photos photo ID of the feed image
   */
  public Long getPhotoId() {
    return getFirst();
  }

  /**
   * @return the String representation of the feed image "URL"
   */
  public String getImageUrlString() {
    return getFirst().toString();
  }

  /**
   * @return the link URL to which the feed image should link
   */
  public URL getLinkUrl() {
    return getSecond();
  }
}
