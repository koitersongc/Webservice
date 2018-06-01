<?php
header('Content-type:text/xml;charset=utf-8');

?>
<webService>
    <head>
        <CardIndex> 8690</CardIndex>

        <marketCode> 92320506MA1P1H4097</marketCode>
        <marketName>苏州亿通在线农贸市场</marketName>
        <tokenNo> 2018010913172869801</tokenNo>
    </head>
    <request>
        <dataList>
            <SalesBill>
                <SalesBillNo> 2018030500000086</SalesBillNo>


                <dealTime>2018-03-05 10:45</dealTime>
                <dealTotalWeight>100.25</dealTotalWeight>
                <BuyerType>4</BuyerType>
                <dealTotalPrice> 120</dealTotalPrice>
                <BuyerCardIndex>4521</BuyerCardIndex>
                <MarketLicenseNo> 91320508MA1P664D26</MarketLicenseNo>
                <BuyerLicenseNo> JY13205050047330</BuyerLicenseNo>
                <BuyerUnitName>新民桥菜市场</BuyerUnitName >
                <PayResult>1</PayResult>
                <Remarks>备注</Remarks>
                <CreateDate>2018-03-05 10:45</CreateDate>
                <SalesBillDetails>
                    <SalesBillDetail>
                        <SalesItemCode>03</SalesItemCode>
                        <SubItemCode>03005</SubItemCode>
                        <SubItemName>大白菜</SubItemName>
                        <UnitPrice>0.8</UnitPrice>
                        <Weight>100</Weight>
                        <TotalPrice>80</TotalPrice>
                        <ProductionFrom>江苏苏州</ProductionFrom>
                    </SalesBillDetail>
                    <SalesBillDetail>
                        <SalesItemCode>03</SalesItemCode>
                        <SubItemCode>03006</SubItemCode>
                        <SubItemName>白萝卜</SubItemName>
                        <UnitPrice>0.4</UnitPrice>
                        <Weight>100</Weight>
                        <TotalPrice>40</TotalPrice>
                        <ProductionFrom>山东寿光</ProductionFrom>
                    </SalesBillDetail>
                </SalesBillDetails>
            </SalesBill>
        </dataList>
    </request>
</webService>