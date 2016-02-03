#include <QTextStream>
#include <QFile>
#include <QString>
#include <QUrl>

int main() {

 QTextStream(stdout) << "Generating..." << endl;
 QString filename = "com.zone-latest";
 QFile file(filename);
 if(!file.open(QIODevice::ReadOnly)) {
  QTextStream(stdout) << "Could not open zone-file" << endl;
  return 1;
 }
 QTextStream data(&file);
 qint32 inside = 0;
 qint32 outside = 0;
 qint32 amount = 0;
 qint32 c = 0;

 QString outputfile_name="temp/outside_nodedup.txt";
 QFile outputfile( outputfile_name );
 if ( outputfile.open(QIODevice::ReadWrite) ) {
   QTextStream output( &outputfile );
   while (!data.atEnd()) {
     QString line = data.readLine();
     if ((!(line.at(0)==';'))&&(!line.isEmpty())) {
       QStringList record = line.split(" ");
       if ((record.count()>2)&&(record.at(1).compare("NS")==0)) {
         //When we reach this we know it's an NS record.
         amount++;
         QString domain = record.at(0)+".COM"; //Add .COM to get complete domains for use in the next step
         QString server = record.at(2);
         if (server.endsWith(".")) { //Inside pointing records end without a dot
           server.chop(1); //Remove the dot at the end
           outside++;
           QUrl server_url;
           QString tld;
           QString registerabledomain;
           server_url.setHost(server);
           tld = server_url.topLevelDomain();
           QString t1 = server;
           t1.chop(tld.length()); //Remove tld
           QStringList t2 = t1.split(".");
           t1 = t2.at(t2.count()-1);
           registerabledomain = t1+tld;
           server = server.toLower();
           tld = tld.toLower();
           registerabledomain = registerabledomain.toLower();
           output << server << "|" << tld << "|" << registerabledomain << endl;
         } else {
           inside++;
           //Aaand... ignore!
         }
       }
     }
     c++;
     if (c>10000000) {
       c = 0;
       QTextStream(stdout) << "[" << QString::number(inside) << "/" << QString::number(outside) << "]" << endl;
     }
   }
 }
 file.close();
 outputfile.close();

 //Done!
 QTextStream(stdout) << "Amount of NS records: " << QString::number(amount) << endl;
 QTextStream(stdout) << "Inside: " << QString::number(inside) << endl;
 QTextStream(stdout) << "Outside: " << QString::number(outside) << endl;
 return 0;
}
