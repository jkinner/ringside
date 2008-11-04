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
public class FeedUserPhoto extends Pair<Integer, URL>
  implements IFeedImage {

  /**
   * Creates a linked Facebook Photos photo to appear in a user's newsfeed/minifeed.
   * 
   * @param userId the photo ID of a Facebook photo to appear in a user's newsfeed/minifeed
   * @param link the URL to which the image should link
   * @see IFacebookRestClient#photos_get
   * @see FacebookRestClient#handleFeedImages
   */
  public FeedUserPhoto(Integer userId, URL link) {
    super(userId, link);
    if (null == userId || null == link) {
      throw new IllegalArgumentException("Both userId and linkUrl should be provided");
    }
    if (0 >= userId) {
      throw new IllegalArgumentException("photoId should be a Facebook user ID");
    }
  }

  /**
   * @return the Facebook user ID of the feed image
   */
  public Integer getUserId() {
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
