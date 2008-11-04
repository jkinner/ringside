
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class BaseJavaClientTestCase extends TestCase
{
   
   protected FacebookRestClient m_client;
      
   public void setUp() throws Exception
   {
      String rtype = JavaClientTestRunner.RESPONSE_TYPE;
      if (!("JSON".equals(rtype) || "XML".equals(rtype))) {
         fail("Unrecognized response type '" + rtype + "'");
      }
      initClient("17001", rtype);
   }
   
   protected void initClient(String uid) throws Exception
   {
      initClient(uid, JavaClientTestRunner.RESPONSE_TYPE);
   }
   
   protected void initClient(String uid, String respType) throws Exception
   {
      String apiKey = "test_case_key-17100";
      String secretKey = "secretkey";
            
      m_client = null;
      if ("XML".equals(respType)) {
         m_client = new FacebookXmlRestClient(apiKey, secretKey);
      } else if ("JSON".equals(respType)) {
         m_client = new FacebookJsonRestClient(apiKey, secretKey);
      } else {
         throw new Exception("Couldn't initialize client, unknown response type '" + respType + "'");
      }
      m_client.setIsDesktop(false);
   
      String authToken = m_client.auth_createToken();
      assertTrue((authToken != null) && (authToken.length() > 0));      
      String resp = m_client.auth_approveToken(authToken, uid);
      assertEquals(resp, "1");      
      
      resp = m_client.auth_getSession(authToken);
      assertNotNull(resp);
      
      assertNotNull(m_client);
   }
   
   public void tearDown() throws Exception
   {  
      
   }
   
   public static Node getChildNodeByName(Node n, String name)
   {
      Node cnode;
      NodeList clist = n.getChildNodes();
      for (int k = 0; k < clist.getLength(); k++) {
         cnode = clist.item(k);
         if (name.equals(cnode.getNodeName())) {
            return cnode;
         }
      }
      return null;
   }
   
   public static Map<String,String> extractObjectFields(Object resp, String[] flds, int indx)
   {
      return extractObjectFields(resp, flds, indx, null);
   }
   
   public static Map<String,String> extractObjectFields(Object resp, String[] flds, int indx,  String subFieldName)
   {
      Map<String,String> valMap = new HashMap<String,String>();
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         Node p = clist.item(indx);
         if (subFieldName != null) {
            p = getChildNodeByName(p, subFieldName);
            if (p == null) fail("Could not find child node named '" + subFieldName + "'");
         }
         Node n;
         for (int k = 0; k < flds.length; k++) {
            n = getChildNodeByName(p, flds[k]); 
            if (n == null) fail("Could not find element '" + flds[k] + "'");
            valMap.put(flds[k], n.getTextContent());
         }
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(indx);
         if (subFieldName != null) {
            obj = (JSONObject) obj.get(subFieldName);
            if (obj == null) fail("Could not find sub-field '" + subFieldName + "'");
         }
         for (int k = 0; k < flds.length; k++) {
            valMap.put(flds[k], obj.get(flds[k]).toString());
         }
      }
      return valMap;
   }
   
}

