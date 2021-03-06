var darwinFields = [
    "ScientificName",
    "Family",
    "Species",
    "Subspecies",
    "Kingdom",
    "Phylum",
    "DateLastModified",
    "InstitutionCode",
    "CollectionCode",
    "CatalogNumber",
    "BasisOfRecord",
    "Class",
    "Order",
    "Genus",
    "ScientificNameAuthor",
    "IdentifiedBy",
    "YearIdentified",
    "MonthIdentified",
    "DayIdentified",
    "TypeStatus",
    "CollectorNumber",
    "FieldNumber",
    "Collector",
    "YearCollected",
    "MonthCollected",
    "DayCollected",
    "JulianDay",
    "TimeOfDay",
    "ContinentOcean",
    "Country",
    "StateProvince",
    "County",
    "Locality",
    "Longitude",
    "Latitude",
    "CoordinatePrecision",
    "MinimumElevation",
    "MaximumElevation",
    "MinimumDepth",
    "MaximumDepth",
    "Sex",
    "PreparationType",
    "IndividualCount",
    "PreviousCatalogNumber",
    "RelationshipType",
    "RelatedCatalogItem",
    "Notes"
];

var digirUrls = [
        "http://arctos.database.museum:80/DiGIRprov/www/DiGIR.php",
    "http://digir.calacademy.org:80/digir/DiGIR.php",
    "http://digir.cumv.cornell.edu:80/digir/DiGIR.php",
    "http://digir.flmnh.ufl.edu:80/manis/DiGIR.php",
    "http://digir.mcz.harvard.edu:80/digir/DiGIR.php",
    "http://digir.rom.on.ca:80/digir/DiGIR.php",
    "http://secure.fhsu.edu:80/digir/DiGIR.php",
    "http://digir.nhm.ku.edu:80/digir/DiGIR.php",
    "http://www.manis.ummz.lsa.umich.edu:80/DiGIRprov/www/DiGIR.php",
    "http://biosrvapp00.utep.edu:80/digir/DiGIR.php",
    "http://manis.umnh.utah.edu:80/DiGIRprov/www/DiGIR.php",
    "http://digir.flmnh.ufl.edu:80/herpnet/DiGIR.php",
    "http://www.inhs.uiuc.edu:80/digir/DiGIR.php",
    "http://lipan.snomnh.ou.edu:80/digir/DiGIR.php",
    "http://herpy.tamu.edu:80/digir/DiGIR.php",
    "http://akn.ornith.cornell.edu:80/digir/DiGIR.php",
    "http://ornis.eeb.ucla.edu:80/digir/DiGIR.php",
    "http://digir.nhm.ku.edu:80/digir/digir.php",
    "http://unibio.ibiologia.unam.mx:8081/digir/DiGIR.php",
    "http://fishnet.ucsd.edu:80/DiGIRprov/www/DiGIR.php",
    "http://www.deh.gov.au/biodiversity/digir/www/DiGIR.php",
    "http://ak.aoos.org:9000/digir/DiGIR.php",
    "http://www.anbg.gov.au/digir/www/DiGIR.php",
    "http://aadc-maps.aad.gov.au/digir/digir.php",
    "http://digir.bebif.be/main/DiGIR.php",
    "http://www.bioshare.net/DigirProvider/",
    "http://www.bsc-eoc.org/digir/www/digir.php",
    "http://digir.andesamazon.org/digir/DiGIR.php",
    "http://digir.chin.gc.ca/digir/DiGIR.php",
    "http://www.cbif.gc.ca/digir/www/DiGIR.php",
    "http://dataprovider.kisti.re.kr/DiGIRprov/www/DiGIR.php",
    "http://collections2.carnegiemnh.org/digir/DiGIR.php",
    "http://digir.nbm-mnb.ca/digir/DiGIR.php",
    "http://digir.sbcollections.org/digir/DiGIR.php",
    "http://slimemold.uark.edu:8080/digir/DiGIR.php",
    "http://www.virtualherbarium.org/digir/digir.php",
    "http://dsibib.mnhn.fr/ici/gicim",
    "http://norbif.uio.no:8080/digir/DiGIR.php",
    "http://granatensis.ugr.es:5000/digir/digir.php",
    "http://taray.csic.es:6000/digir/DiGIR.php",
    "http://utc.usu.edu:8080/digir/DiGIR.php",
    "http://icomm.mbl.edu/digir/DiGIR.php",
    "http://gbif.ddbj.nig.ac.jp:8080/digir/DiGIR.php",
    "http://www.kew.org/digir/www/DiGIR.php",
    "http://gbif.zbs.bialowieza.pl:5001/digir/DiGIR.php",
    "http://digir.mobot.org/digir/DiGIR.php",
    "http://gbif.science-net.kahaku.go.jp:8080/digir/DiGIR.php",
    "http://services.natureserve.org/digir/DiGIR.php",
    "http://iobis.marine.rutgers.edu/digir2/DiGIR.php",
    "http://atbi.biosci.ohio-state.edu/cgi-bin/chi/digir.plx",
    "http://web.science.oregonstate.edu:8080/digir/DiGIR.php",
    "http://digir.austmus.gov.au/ozcam/DiGIR.php",
    "http://serfis.by.ua.edu:85/digir/DiGIR.php",
    "http://www.unav.es/unzyec/digir/DiGIR.php",
    "http://wdcm.nig.ac.jp:8080/digir/DiGIR.php",
    "http://aadc-maps.aad.gov.au/digir/DiGIR.php",
    "http://obis.env.duke.edu/digir/DiGIR.php",
    "http://pluto.kgs.ku.edu/digir/DiGIR.php",
    "http://splink.cria.org.br/brobis/DiGIR.php",
    "http://webmap.niwa.co.nz/digir/DiGiR.php",
    "http://www.ifremer.fr/digir/DiGIR.php",
    "http://www.noc.soton.ac.uk/chess/digir/DiGIR.php",
    "http://www.obis.org.au/digir/DiGIR.php",
    "http://mlbean.byu.edu:80/digir/DiGIR.php",
    "http://herbarium.lib.msu.edu/digir/DiGIR.php",
    "http://digir.nerc-bas.ac.uk/DiGIR.php",
    "http://argbif.cenpat.gov.ar/digir/DiGIR.php",
    "http://plantsws.nrcs.usda.gov/digir/DiGIR.php",
    "http://digir.sunsite.utk.edu:8080/digir/DiGIR.php",
    "http://conabioweb.conabio.gob.mx:8050/digir/DiGIR.php",
    "http://chrysemys.unm.edu/digir/DiGIR.php",
    "http://gbif.bio.jyu.fi/DigirProvider/index_html",
    "http://granatensis.ugr.es:5000/digir/DIGIR.php",
    "http://conabioweb.conabio.gob.mx:8050/digir/DIGIR.php",
    "http://biology.burke.washington.edu:16080/digir/burke/DiGIR.php",
    "http://herpnet.ua.edu:80/DiGIRprov/DiGIR.php",
    "http://biology.burke.washington.edu/digir/DiGIR.php",
    "http://blb.biosci.ohio-state.edu:80/digir/DiGIR.php",
    "http://www.fishbase.ph/digirprov/www/DiGIR.php",
    "http://acoi.ci.uc.pt/digir/www/DiGIR.php",
    "http://loco2.biosci.arizona.edu:80/digir/DiGIR.php",
    "http://digir.acnatsci.org:80/digir/DiGIR.php",
    "http://enuvis05.fulton.asu.edu:80/digir/DiGIR.php",
    "http://www.georef.naturkundemuseum-bw.de/digir/www/DiGIR.php",
    "http://pythonprovider.danbif.dk/DigirProvider/index_html",
    "http://portalsplink.cria.org.br/prov_pollinator/DiGIR.php",
    "http://pythonprovider.danbif.dk/botanik/DigirProvider/index_html",
    "http://www.musitutv.uio.no/digir/DiGIR.php",
    "http://data.kew.org/digir/www/DiGIR.php",
    "http://aqua.ajou.ac.kr:150/digir/DiGIR.php"
];
