
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientFriendsTestCase extends BaseJavaClientTestCase
{  
   public void testFriendsAreFriends() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.friends_areFriends(17001, 17002);     
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         clist = clist.item(0).getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("uid1", n.getNodeName());
         assertEquals("17001", n.getTextContent());
         n = clist.item(1);
         assertEquals("uid2", n.getNodeName());
         assertEquals("17002", n.getTextContent());
         n = clist.item(2);
         assertEquals("are_friends", n.getNodeName());
         assertEquals("1", n.getTextContent());
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         assertEquals("17001", obj.get("uid1"));
         assertEquals("17002", obj.get("uid2"));
         assertEquals("1", obj.get("are_friends"));
      }
      
      resp = m_client.friends_areFriends(17001, 17003);     
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         clist = clist.item(0).getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("uid1", n.getNodeName());
         assertEquals("17001", n.getTextContent());
         n = clist.item(1);
         assertEquals("uid2", n.getNodeName());
         assertEquals("17003", n.getTextContent());
         n = clist.item(2);
         assertEquals("are_friends", n.getNodeName());
         assertEquals("1", n.getTextContent());
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         assertEquals("17001", obj.get("uid1"));
         assertEquals("17003", obj.get("uid2"));
         assertEquals("1", obj.get("are_friends"));
      }
      
      resp = m_client.friends_areFriends(17001, 17004);     
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         clist = clist.item(0).getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("uid1", n.getNodeName());
         assertEquals("17001", n.getTextContent());
         n = clist.item(1);
         assertEquals("uid2", n.getNodeName());
         assertEquals("17004", n.getTextContent());
         n = clist.item(2);
         assertEquals("are_friends", n.getNodeName());
         assertEquals("1", n.getTextContent());
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         assertEquals("17001", obj.get("uid1"));
         assertEquals("17004", obj.get("uid2"));
         assertEquals("1", obj.get("are_friends"));
      }
      
      resp = m_client.friends_areFriends(17001, 17005);     
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         clist = clist.item(0).getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("uid1", n.getNodeName());
         assertEquals("17001", n.getTextContent());
         n = clist.item(1);
         assertEquals("uid2", n.getNodeName());
         assertEquals("17005", n.getTextContent());
         n = clist.item(2);
         assertEquals("are_friends", n.getNodeName());
         assertEquals("0", n.getTextContent());
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         assertEquals("17001", obj.get("uid1"));
         assertEquals("17005", obj.get("uid2"));
         assertEquals("0", obj.get("are_friends"));
      }
      
      resp = m_client.friends_areFriends(17001, 17999);     
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         clist = clist.item(0).getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("uid1", n.getNodeName());
         assertEquals("17001", n.getTextContent());
         n = clist.item(1);
         assertEquals("uid2", n.getNodeName());
         assertEquals("17999", n.getTextContent());
         n = clist.item(2);
         assertEquals("are_friends", n.getNodeName());
         assertEquals("0", n.getTextContent());
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         assertEquals("17001", obj.get("uid1"));
         assertEquals("17999", obj.get("uid2"));
         assertEquals("0", obj.get("are_friends"));
      }
      m_client.setDebug(false);
   }
   
   public void testFriendsAreAppUsers() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.friends_getAppUsers();
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(2, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("17002", n.getTextContent());
         n = clist.item(1);
         assertEquals("17003", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {         
         JSONArray arr = (JSONArray) resp;
         assertEquals("17002", arr.get(0));
         assertEquals("17003", arr.get(1));
      }      
      m_client.setDebug(false);
   }
}