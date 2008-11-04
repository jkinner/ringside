
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientTestRunner
{  
   public static String RESPONSE_TYPE;
   
   public static void main(String args[])
   {  
      TestSuite suite = new TestSuite();
      suite.addTestSuite(JavaClientFriendsTestCase.class);
      suite.addTestSuite(JavaClientUsersTestCase.class);
      suite.addTestSuite(JavaClientNotificationsTestCase.class);
      suite.addTestSuite(JavaClientEventsTestCase.class);
      suite.addTestSuite(JavaClientProfileTestCase.class);
      suite.addTestSuite(JavaClientFbmlTestCase.class);
      suite.addTestSuite(JavaClientPagesTestCase.class);
      suite.addTestSuite(JavaClientPhotosTestCase.class);
      suite.addTestSuite(JavaClientGroupsTestCase.class);
      suite.addTestSuite(JavaClientFeedTestCase.class);
      
      RESPONSE_TYPE = args[0];
      System.err.println("[JavaClientTestRunner] Running tests for " + RESPONSE_TYPE + " response format...");
      junit.textui.TestRunner.run(suite);
   }
}
