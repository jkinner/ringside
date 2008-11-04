/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 * 
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attribution
 * statements applied by the authors.  All third-party contributions are
 * distributed under license by Ringside Networks, Inc.
 * 
 * This is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of
 * the License, or (at your option) any later version.
 * 
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free
 * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
 ******************************************************************************/

function update_strength(input_field_id,output_display_id)
{
	var input_field=document.getElementById(input_field_id);
	var output_display=document.getElementById(output_display_id);
	var input_str=input_field.value;
	var input_length=input_str.length;
	var strength=0;
	number_re=new RegExp('[0-9]');
	if(number_re.test(input_str)){strength++;}
	non_alpha_re=new RegExp('[^A-Za-z0-9]');
	if(non_alpha_re.test(input_str)){strength++;}
	upper_alpha_re=new RegExp("[A-Z]");
	if(upper_alpha_re.test(input_str)){strength++;}
	if(input_length>=8){strength++;}

	var strength_str='<text>Password Strength</text>';

	if(strength<=1)
	{
		strength_str=strength_str+'<strong style="color:grey">Weak</strong>';
	}else if(strength<=2)
	{
		strength_str=strength_str+'<strong style="color:blue">Medium</strong>';
	}else
	{
		strength_str=strength_str+'<strong style="color:green">Strong</strong>';
	}
	if(input_str.length<6){strength_str='<strong style=\"color:orange\">too_short</strong>';}
	output_display.innerHTML=strength_str;

}
