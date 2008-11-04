
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientUsersTestCase extends BaseJavaClientTestCase
{  
   public void testUsersGetLoggedInUser() throws Exception
   {
      int uid = m_client.users_getLoggedInUser();
      assertEquals(17001, uid);
   }
   
   public void testUsersIsAppAdded() throws Exception
   {
      //m_client.setDebug(true);
      assertTrue(m_client.users_isAppAdded());
      m_client.setDebug(false);
   }
   
   public void testUsersHasAppPermission() throws Exception
   {
      Object resp;
      Document xresp;
      String sresp = null;
      
      resp = m_client.callMethod(FacebookMethod.USERS_HAS_APP_PERMISSION,
                                 new Pair<String,CharSequence>("ext_perm", "status_update"));
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;         
         sresp = xresp.getDocumentElement().getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {         
         sresp = (String) resp;
      }      
      assertEquals("1", sresp);
      
      resp = m_client.callMethod(FacebookMethod.USERS_HAS_APP_PERMISSION,
                                 new Pair<String,CharSequence>("ext_perm", "photo_upload"));
      sresp = null;
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;         
         sresp = xresp.getDocumentElement().getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {         
         sresp = (String) resp;
      }      
      assertEquals("0", sresp);
      
      resp = m_client.callMethod(FacebookMethod.USERS_HAS_APP_PERMISSION,
            new Pair<String,CharSequence>("ext_perm", "create_listing"));
      sresp = null;
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;         
         sresp = xresp.getDocumentElement().getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {         
         sresp = (String) resp;
      }      
      assertEquals("1", sresp);
   }
   
   public void testUsersSetStatus() throws Exception
   {  
      Object resp;
      Document xresp;
      JSONObject jresp;
      
      List<Integer> uids = new ArrayList<Integer>();
      uids.add(17001);
      Set<CharSequence> fields = new HashSet<CharSequence>();
      fields.add("status");
      fields.add("uid");
      
      String sval = null;
      resp = m_client.users_getInfo(uids, fields);

      
      //get old status
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;     
         NodeList clist = xresp.getElementsByTagName("status");
         assertNotNull(clist);
         assertTrue(clist.getLength() == 1);
         Node m = getChildNodeByName(clist.item(0), "message");
         assertNotNull(m);
         sval = m.getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {                  
         JSONArray arr = (JSONArray) resp;
         JSONObject uobj = (JSONObject) arr.get(0);
         JSONObject sobj = (JSONObject) uobj.get("status");
         sval = (String) sobj.get("message");
      } else {
         fail("Unknown response type '" + JavaClientTestRunner.RESPONSE_TYPE + "'");
      }
      String oldMessage = sval;
      assertEquals("CooCoo for Coco Puffs", oldMessage);
      
      resp = m_client.callMethod(FacebookMethod.USERS_SET_STATUS,
                                 new Pair<String,CharSequence>("status", "writing code"),
                                 new Pair<String,CharSequence>("clear", "true"));
      
      resp = m_client.users_getInfo(uids, fields);
      //get changed status
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;     
         NodeList clist = xresp.getElementsByTagName("status");
         assertNotNull(clist);
         assertTrue(clist.getLength() == 1);
         Node m = getChildNodeByName(clist.item(0), "message");
         assertNotNull(m);
         sval = m.getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {                  
         JSONArray arr = (JSONArray) resp;
         JSONObject uobj = (JSONObject) arr.get(0);
         JSONObject sobj = (JSONObject) uobj.get("status");
         sval = (String) sobj.get("message");
      }
      assertEquals("writing code", sval);
      
      resp = m_client.callMethod(FacebookMethod.USERS_SET_STATUS,
            new Pair<String,CharSequence>("status", oldMessage),
            new Pair<String,CharSequence>("clear", "true"));
      resp = m_client.users_getInfo(uids, fields);
      //get changed status
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         xresp = (Document) resp;     
         NodeList clist = xresp.getElementsByTagName("status");
         assertNotNull(clist);
         assertTrue(clist.getLength() == 1);
         Node m = getChildNodeByName(clist.item(0), "message");
         assertNotNull(m);
         sval = m.getTextContent();
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {                  
         JSONArray arr = (JSONArray) resp;
         JSONObject uobj = (JSONObject) arr.get(0);
         JSONObject sobj = (JSONObject) uobj.get("status");
         sval = (String) sobj.get("message");
      }
      assertEquals(oldMessage, sval);
   }
   
   public void testUsersGetInfo() throws Exception
   {
      //m_client.setDebug(true);
      
      List<Integer> uids = new ArrayList<Integer>();
      uids.add(17001);
      Set<CharSequence> fields = new HashSet<CharSequence>();
      fields.add("about_me"); fields.add("activities"); fields.add("affiliations");
      fields.add("birthday"); fields.add("books"); fields.add("current_location");
      fields.add("education_history"); fields.add("first_name"); fields.add("is_app_user");
      fields.add("has_added_app"); fields.add("hometown_location"); 
      fields.add("interests"); fields.add("last_name"); fields.add("meeting_for");
      fields.add("meeting_sex"); fields.add("movies"); fields.add("music");
      fields.add("name"); fields.add("notes_count"); fields.add("pic");
      fields.add("pic_big"); fields.add("pic_small"); fields.add("pic_square");
      fields.add("political"); fields.add("profile_update_time"); fields.add("quotes");
      fields.add("relationship_status"); fields.add("religion"); fields.add("sex");
      fields.add("significant_other_id"); fields.add("status"); fields.add("timezone");
      fields.add("tv"); fields.add("wall_count"); fields.add("work_history"); fields.add("uid");
      //TODO: fields.add("hs_info");
      
      Object resp = m_client.users_getInfo(uids, fields);
      Document xresp = null;
      JSONObject jresp = null;
      JSONObject uobj = null;
      NodeList clist = null;
      Node n = null;
      JSONObject j = null;
      String[] sfields = new String[] { "about_me","activities","birthday","books","first_name","interests","last_name",
             "movies","music","political","profile_update_time","quotes","relationship_status",
             "religion","sex","significant_other_id","timezone","tv"};
      Map<String,String> svalMap = new HashMap();
      
      //put simple type values in an array
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         xresp = (Document) resp;
     
         for (int k = 0; k < sfields.length; k++) {         
            clist = xresp.getElementsByTagName(sfields[k]);
            assertNotNull(clist);
            if (clist.getLength() > 0) {
               svalMap.put(sfields[k], clist.item(0).getTextContent());
            } else {
               svalMap.put(sfields[k], "");
            }
         }         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {                  
         JSONArray arr = (JSONArray) resp;
         uobj = (JSONObject) arr.get(0);
         String sval; 
         for (int k = 0; k < sfields.length; k++) {
            sval = (String) uobj.get(sfields[k]);            
            if (sval != null) {
               svalMap.put(sfields[k], sval);
            } else {
               svalMap.put(sfields[k], "");
            }
         }
      }
      
      //check simple values
      assertEquals("About me - nothing!", svalMap.get("about_me"));      
      assertEquals("Boating, pumpkin carving, procuring comestibles, etc. ", svalMap.get("activities"));
      assertEquals("2007-12-31", svalMap.get("birthday"));
      assertEquals("Snakes on a plane, the book", svalMap.get("books"));
      assertEquals("Test1", svalMap.get("first_name"));
      assertEquals("Music,sports,tv guide,nuclear physics", svalMap.get("interests"));
      assertEquals("User1", svalMap.get("last_name"));      
      assertEquals("Snakes on a plane", svalMap.get("movies"));      
      assertEquals("Miles Davis,Brittney Spears", svalMap.get("music"));
      assertEquals("Liberal", svalMap.get("political"));
      assertEquals("2008-01-01 00:00:00", svalMap.get("profile_update_time"));    
      assertEquals("Nationalism is an infantile disease - Albert Einstien", svalMap.get("quotes"));      
      assertEquals("Single", svalMap.get("relationship_status"));
      assertEquals("Scientologist", svalMap.get("religion"));
      assertEquals("M", svalMap.get("sex"));
      assertEquals("0", svalMap.get("significant_other_id"));      
      assertEquals("-4", svalMap.get("timezone"));
      assertEquals("Telemundo", svalMap.get("tv"));
      
      //check out affiliations
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("affiliations");
         assertTrue(clist.getLength() == 1);
         clist = clist.item(0).getChildNodes();
         assertTrue(clist.getLength() == 3);
         
         Node aff = clist.item(0);
         assertEquals("affiliation", aff.getNodeName());
         n = getChildNodeByName(aff, "nid");
         assertEquals("1", n.getTextContent());
         n = getChildNodeByName(aff, "name");
         assertEquals("Philadelphia", n.getTextContent());
         
         aff = clist.item(1);
         assertEquals("affiliation", aff.getNodeName());
         n = getChildNodeByName(aff, "nid");
         assertEquals("2", n.getTextContent());
         n = getChildNodeByName(aff, "name");
         assertEquals("Arts and Crafts", n.getTextContent());
         
         aff = clist.item(2);
         assertEquals("affiliation", aff.getNodeName());
         n = getChildNodeByName(aff, "nid");
         assertEquals("3", n.getTextContent());
         n = getChildNodeByName(aff, "name");
         assertEquals("Northeast High", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray affs = (JSONArray) uobj.get("affiliations");         
         
         j = (JSONObject) affs.get(0);
         assertEquals("1", j.get("nid"));
         assertEquals("Philadelphia", j.get("name"));
         
         j = (JSONObject) affs.get(1);
         assertEquals("2", j.get("nid"));
         assertEquals("Arts and Crafts", j.get("name"));
         
         j = (JSONObject) affs.get(2);
         assertEquals("3", j.get("nid"));
         assertEquals("Northeast High", j.get("name"));
      }
      
      //test current location
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("current_location");
         assertTrue(clist.getLength() == 1);         
         Node cloc = clist.item(0);
         
         n = getChildNodeByName(cloc, "city");
         assertNotNull(n);
         assertEquals("Bowmont", n.getTextContent());
         
         n = getChildNodeByName(cloc, "state");
         assertNotNull(n);
         assertEquals("NJ", n.getTextContent());
         
         n = getChildNodeByName(cloc, "country");
         assertNotNull(n);
         assertEquals("USA", n.getTextContent());
         
         n = getChildNodeByName(cloc, "zip");
         assertNotNull(n);
         assertEquals("00181", n.getTextContent());         
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONObject cl = (JSONObject) uobj.get("current_location");         
         
         assertEquals("Bowmont", cl.get("city"));
         assertEquals("NJ", cl.get("state"));
         assertEquals("USA", cl.get("country"));
         assertEquals("00181", cl.get("zip"));
      }
      
      //test education history
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("education_history");
         assertTrue(clist.getLength() == 1);         
         Node ehist = clist.item(0);
         
         NodeList elist = ehist.getChildNodes();
         assertEquals(2, elist.getLength());
         
         Node einfo = elist.item(0);         
         n = getChildNodeByName(einfo, "name");
         assertNotNull(n);
         assertEquals("Temple University", n.getTextContent());         
         n = getChildNodeByName(einfo, "year");
         assertNotNull(n);
         assertEquals("1999", n.getTextContent());         
         n = getChildNodeByName(einfo, "concentrations");
         clist = n.getChildNodes();
         assertEquals(2, clist.getLength());         
         n = clist.item(0);
         assertEquals("Communications", n.getTextContent());         
         n = clist.item(1);
         assertEquals("Philosophy", n.getTextContent());
         
         einfo = elist.item(1);         
         n = getChildNodeByName(einfo, "name");
         assertNotNull(n);
         assertEquals("Georgia Tech", n.getTextContent());         
         n = getChildNodeByName(einfo, "year");
         assertNotNull(n);
         assertEquals("2006", n.getTextContent());         
         n = getChildNodeByName(einfo, "concentrations");
         clist = n.getChildNodes();
         assertEquals(2, clist.getLength());         
         n = clist.item(0);
         assertEquals("Rocket Science", n.getTextContent());         
         n = clist.item(1);
         assertEquals("Awesomeness", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray arr = (JSONArray) uobj.get("education_history");         
         
         JSONObject einfo = (JSONObject) arr.get(0);
         assertEquals("Temple University", einfo.get("name"));
         assertEquals("1999", einfo.get("year"));
         JSONArray concs = (JSONArray) einfo.get("concentrations");
         assertEquals("Communications", concs.get(0));
         assertEquals("Philosophy", concs.get(1));
         
         einfo = (JSONObject) arr.get(1);
         assertEquals("Georgia Tech", einfo.get("name"));
         assertEquals("2006", einfo.get("year"));
         concs = (JSONArray) einfo.get("concentrations");
         assertEquals("Rocket Science", concs.get(0));
         assertEquals("Awesomeness", concs.get(1));
      }
      
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("hometown_location");
         assertTrue(clist.getLength() == 1);         
         Node cloc = clist.item(0);
         
         n = getChildNodeByName(cloc, "city");
         assertNotNull(n);
         assertEquals("Eightown", n.getTextContent());
         
         n = getChildNodeByName(cloc, "state");
         assertNotNull(n);
         assertEquals("NI", n.getTextContent());
         
         n = getChildNodeByName(cloc, "country");
         assertNotNull(n);
         assertEquals("USA", n.getTextContent());
         
         n = getChildNodeByName(cloc, "zip");
         assertNotNull(n);
         assertEquals("87654", n.getTextContent());         
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONObject cl = (JSONObject) uobj.get("hometown_location");         
         
         assertEquals("Eightown", cl.get("city"));
         assertEquals("NI", cl.get("state"));
         assertEquals("USA", cl.get("country"));
         assertEquals("87654", cl.get("zip"));
      }
      
      //test meeting for
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("meeting_for");
         assertEquals(1, clist.getLength());         
         clist = clist.item(0).getChildNodes();
         assertEquals(2, clist.getLength());
         
         n = clist.item(0);
         assertEquals("seeking", n.getNodeName());
         assertEquals("Random Play", n.getTextContent());
         n = clist.item(1);
         assertEquals("seeking", n.getNodeName());
         assertEquals("Whatever I can get", n.getTextContent());                  
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray mfor = (JSONArray) uobj.get("meeting_for");
         assertEquals("Random Play", mfor.get(0));
         assertEquals("Whatever I can get", mfor.get(1));
      }
      
      //test meeting sex
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("meeting_sex");
         assertEquals(1, clist.getLength());         
         clist = clist.item(0).getChildNodes();
         assertEquals(2, clist.getLength());
         
         n = clist.item(0);
         assertEquals("sex", n.getNodeName());
         assertEquals("M", n.getTextContent());
         n = clist.item(1);
         assertEquals("sex", n.getNodeName());
         assertEquals("F", n.getTextContent());                  
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray msex = (JSONArray) uobj.get("meeting_sex");
         assertEquals("M", msex.get(0));
         assertEquals("F", msex.get(1));
      }
      
      //test status message
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("status");
         assertEquals(1, clist.getLength());         
         clist = clist.item(0).getChildNodes();
         assertEquals(2, clist.getLength());
         
         n = clist.item(0);
         assertEquals("message", n.getNodeName());
         assertEquals("CooCoo for Coco Puffs", n.getTextContent());                          
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONObject stat = (JSONObject) uobj.get("status");
         assertEquals("CooCoo for Coco Puffs", stat.get("message"));
      }
           
      //test work history
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         clist = xresp.getElementsByTagName("work_history");
         assertEquals(1, clist.getLength());         
         clist = clist.item(0).getChildNodes();
         assertEquals(2, clist.getLength());         
         Node winfo = clist.item(0);         
         assertEquals("work_info", winfo.getNodeName());
         
         n = getChildNodeByName(winfo, "company_name");
         assertNotNull(n);
         assertEquals("Spacely Sprockets", n.getTextContent());         
         n = getChildNodeByName(winfo, "position");
         assertNotNull(n);
         assertEquals("Sprocket Engineer", n.getTextContent());         
         n = getChildNodeByName(winfo, "description");
         assertNotNull(n);
         assertEquals("Now is the time on sprockets when we dance", n.getTextContent());         
         n = getChildNodeByName(winfo, "start_date");
         assertNotNull(n);
         assertEquals("2002-01-01", n.getTextContent());         
         n = getChildNodeByName(winfo, "end_date");
         assertNotNull(n);
         assertEquals("2003-02-04", n.getTextContent());
         
         Node loc = getChildNodeByName(winfo, "location");         
         n = getChildNodeByName(loc, "city");
         assertEquals("Hairydelphia", n.getTextContent());         
         n = getChildNodeByName(loc, "state");
         assertEquals("PA", n.getTextContent());
         n = getChildNodeByName(loc, "country");
         assertEquals("USA", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {         
         JSONArray whist = (JSONArray) uobj.get("work_history");
         JSONObject winfo = (JSONObject) whist.get(0);
         
         assertEquals("Spacely Sprockets", winfo.get("company_name"));         
         assertEquals("Sprocket Engineer", winfo.get("position"));         
         assertEquals("Now is the time on sprockets when we dance", winfo.get("description"));
         assertEquals("2002-01-01", winfo.get("start_date"));
         assertEquals("2003-02-04", winfo.get("end_date"));
         
         JSONObject wloc = (JSONObject) winfo.get("location");
         assertEquals("Hairydelphia", wloc.get("city"));
         assertEquals("PA", wloc.get("state"));
         assertEquals("USA", wloc.get("country"));
      }
      
      m_client.setDebug(false);
   }
   
   public void testFriendsGet() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.friends_get();
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(3, clist.getLength());
         
         Node n = clist.item(0);
         assertEquals("17002", n.getTextContent());         
         n = clist.item(1);
         assertEquals("17003", n.getTextContent());
         n = clist.item(2);
         assertEquals("17004", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {
         JSONArray arr = (JSONArray) resp;
         assertEquals("17002", arr.get(0));
         assertEquals("17003", arr.get(1));
         assertEquals("17004", arr.get(2));
      }
      m_client.setDebug(false);
   }
}