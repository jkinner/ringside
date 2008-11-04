package com.facebook.api;

import java.net.URL;

/**
 * Interface for an image that appears in a newsfeed/minifeed story and the 
 * destination URL for a click on that image.
 * 
 * @see IFacebookRestClient
 * @see FacebookRestClient#handleFeedImages
 */
public interface IFeedImage {
  /**
   * The image "url" can be either:
   * <ul>
   * <li> a URL linking to an image: this image will be 
   *    be shrunk to fit within 75x75, cached, and formatted by Facebook. </li>
   * <li> a Facebook photo ID</li>
   * </ul>
   * @return the String representation of the feed image URL
   */
  public String getImageUrlString();

  /**
   * @return the URL to which the feed image should link
   */
  public URL getLinkUrl();
}
