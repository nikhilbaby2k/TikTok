import java.awt.Toolkit;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.StringSelection;
import java.awt.datatransfer.DataFlavor;
import java.awt.event.*;
import java.awt.Robot;

public class TaskToPerform {

    public static void main(String[] args) throws Exception {		
		int i=0;
		int MouseXPos=0;
		int MouseYPos=0;
		
		Robot bot = null;
 		try {
 			 bot = new Robot();
 		} catch (Exception failed) {
  			System.err.println("Failed instantiating Robot: " + failed);
 		}

		while(i<args.length)
		{		
			if(isNumeric(args[i]))
			{
				MouseXPos=Integer.parseInt(args[i]);
				i++;
				MouseYPos=Integer.parseInt(args[i]);

				bot.mouseMove(MouseXPos, MouseYPos);
			}
			else if(args[i].equals("MAXCLOSE"))
			{
				bot.keyPress(KeyEvent.VK_ALT);
				bot.keyPress(KeyEvent.VK_F4);
				bot.keyRelease(KeyEvent.VK_F4);
				bot.keyRelease(KeyEvent.VK_ALT);	
				
				toWait(1);	
			}
			else if(args[i].equals("UPKEY"))
			{
				bot.keyPress(KeyEvent.VK_UP);
				bot.keyRelease(KeyEvent.VK_UP);		
			}
			else if(args[i].equals("DOWNKEY"))
			{
				bot.keyPress(KeyEvent.VK_DOWN);
				bot.keyRelease(KeyEvent.VK_DOWN);		
			}
			else if(args[i].equals("INSPECTELEMENT"))
			{
				bot.keyPress(KeyEvent.VK_CONTROL);
				bot.keyPress(KeyEvent.VK_Q);
				bot.keyRelease(KeyEvent.VK_Q);				
				bot.keyRelease(KeyEvent.VK_CONTROL);
				toWait(6);

				bot.mouseMove(1056, 663);
				bot.mousePress(InputEvent.BUTTON3_MASK);
        			bot.mouseRelease(InputEvent.BUTTON3_MASK);
				toWait(1);

				bot.keyPress(KeyEvent.VK_CONTROL);
				bot.keyPress(KeyEvent.VK_U);
				bot.keyRelease(KeyEvent.VK_U);				
				bot.keyRelease(KeyEvent.VK_CONTROL);
				toWait(1);
				
				String data = (String)Toolkit.getDefaultToolkit().getSystemClipboard().getData(DataFlavor.stringFlavor);
				
				i++;				
				String[] temp = args[i].split("---");
				String finalDiv=null;
				int posIndex=Integer.parseInt(temp[0]);
				if(posIndex > 0)
				{					
					String[] parts = data.split(" > ");
					String[] finalParts=new String[posIndex];					
					for(int k=0; k<posIndex;k++)
					{
						finalParts[k]=parts[k];				
					}
					finalDiv=combine(finalParts, " > ");
					
					if(temp.length>1)
					{
						String toConcat=temp[1];
						finalDiv+=" > "+toConcat;	
					}
				}
				else
				{
					finalDiv=data;	
				}

				bot.mouseMove(113, 760);
				bot.mousePress(InputEvent.BUTTON1_MASK);
        			bot.mouseRelease(InputEvent.BUTTON1_MASK);
				toWait(1);

				bot.keyPress(KeyEvent.VK_F12);
				bot.keyRelease(KeyEvent.VK_F12);
				System.out.print(finalDiv);
			}
			else if(args[i].equals("LEFTCLICK"))
			{
				bot.mousePress(InputEvent.BUTTON1_MASK);
        			bot.mouseRelease(InputEvent.BUTTON1_MASK);
				
				toWait(1);
			}
			else if(args[i].equals("RIGHTCLICK"))
			{
				bot.mousePress(InputEvent.BUTTON3_MASK);
        			bot.mouseRelease(InputEvent.BUTTON3_MASK);
				
				toWait(1);
			}	
			else if(args[i].equals("MAXSELECT"))
			{				
				bot.keyPress(KeyEvent.VK_CONTROL);
				bot.keyPress(KeyEvent.VK_A);
				bot.keyRelease(KeyEvent.VK_A);
				bot.keyRelease(KeyEvent.VK_CONTROL);
				
				toWait(1);
			}
			else if(args[i].equals("MAXCOPY") || args[i].equals("MAXSHIFTCOPY"))
			{
				bot.keyPress(KeyEvent.VK_CONTROL);
				if(args[i].equals("MAXSHIFTCOPY"))
					bot.keyPress(KeyEvent.VK_SHIFT);
				bot.keyPress(KeyEvent.VK_C);
				bot.keyRelease(KeyEvent.VK_C);
				if(args[i].equals("MAXSHIFTCOPY"))
					bot.keyRelease(KeyEvent.VK_SHIFT);				
				bot.keyRelease(KeyEvent.VK_CONTROL);	
				
				toWait(1);
			}
			else if(args[i].equals("MAXPASTE") || args[i].equals("MAXSHIFTPASTE"))
			{
				bot.keyPress(KeyEvent.VK_CONTROL);
				if(args[i].equals("MAXSHIFTPASTE"))
					bot.keyPress(KeyEvent.VK_SHIFT);				
				bot.keyPress(KeyEvent.VK_V);
				bot.keyRelease(KeyEvent.VK_V);
				if(args[i].equals("MAXSHIFTPASTE"))
					bot.keyRelease(KeyEvent.VK_SHIFT);				
				bot.keyRelease(KeyEvent.VK_CONTROL);				
				
				toWait(1);
			}
			else if(args[i].equals("MAXDELETE"))
			{
				bot.keyPress(KeyEvent.VK_DELETE);
  				bot.keyRelease(KeyEvent.VK_DELETE);
				
				toWait(1);				
			}
			else if(args[i].equals("MAXENTER"))
			{
				bot.keyPress(KeyEvent.VK_ENTER);
  				bot.keyRelease(KeyEvent.VK_ENTER);
				
				toWait(1);			
			}
			else
			{
				Clipboard clipBoard = Toolkit.getDefaultToolkit().getSystemClipboard();
        			StringSelection data = new StringSelection(args[i]);
        			clipBoard.setContents(data, data);
			}
			i++;
		}
    }
	
	public static boolean isNumeric(String str)
	{
  		return str.matches("\\d+");
	}
	
	public static void toWait(int inSec)
	{
		int inmSec=inSec * 1000;
	        try {
		    java.lang.Thread.sleep(inmSec);
		} catch (InterruptedException e) {
			e.printStackTrace();
			System.err.println("Failed instantiating Sleep: ");
		}
	}
	
	public static String combine(String[] s, String glue)
	{
		int k = s.length;
		if ( k == 0 )
		{
			return null;
		}
		StringBuilder out = new StringBuilder();
		out.append( s[0] );
		for ( int x=1; x < k; ++x )
		{
			out.append(glue).append(s[x]);
		}
		return out.toString();
	}
}
