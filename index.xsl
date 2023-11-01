<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes"/>

    <xsl:variable name="selectedState" select="''"/>

    <xsl:template match="/">
        <html>
            <head>
                <title>Assets</title>
            </head>
            <body>
                <h1>Assets</h1>
                <form>
                    <label for="stateFilter">Select State:</label>
                    <select name="stateFilter" id="stateFilter" onchange="submit()">
                        <option value="" selected="selected">All</option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                    </select>
                </form>
                <table border="1">
                    <tr>
                        <th>Number</th>
                        <th>Name</th>
                        <th>State</th>
                        <th>Cost</th>
                        <th>Responsible Person</th>
                        <th>Additional Information</th>
                    </tr>
                    <xsl:apply-templates select="assets/asset">
                        <xsl:with-param name="selectedState" select="$selectedState"/>
                    </xsl:apply-templates>
                </table>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="asset">
        <xsl:param name="selectedState"/>
        <xsl:if test="$selectedState = '' or state = $selectedState">
            <tr>
                <td><xsl:value-of select="number" /></td>
                <td><xsl:value-of select="name" /></td>
                <td><xsl:value-of select="state" /></td>
                <td><xsl:value-of select="cost" /></td>
                <td><xsl:value-of select="responsible_person" /></td>
                <td><xsl:value-of select="additional_information" /></td>
            </tr>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
