<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

    <xsl:param name="stateFilter" select="''"/>
    <xsl:param name="personFilter" select="''"/>

    <xsl:template match="/">

                <xsl:apply-templates select="assets/asset[state=$stateFilter or $stateFilter = ''][contains(responsible_person, $personFilter)]" />
    </xsl:template>
    <xsl:template match="asset">
        <xsl:param name="stateFilter"/>
        <xsl:param name="personFilter"/>
        <xsl:variable name="currentState" select="state"/>
        <xsl:if test="($stateFilter = '' or $stateFilter = 'All' or $stateFilter = $currentState) and (contains(responsible_person, $personFilter) or $personFilter = '')">
            <tr>
                <td><xsl:value-of select="number" /></td>
                <td><xsl:value-of select="name" /></td>
                <td><xsl:value-of select="state" /></td>
                <td><xsl:value-of select="cost" /></td>
                <td><xsl:value-of select="responsible_person" /></td>
                <td><xsl:value-of select="additional_information" /></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="rowIndex" value="{position() - 1}" />
                        <input type="submit" id="delete" name="delete" value="Delete" />
                    </form>
                </td>
            </tr>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
