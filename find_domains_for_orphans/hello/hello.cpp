#include <QTextStream>
#include <QFile>
#include <QString>
#include <QUrl>
#include <QMap>

qint32 outputcounter = 0;
QMap<QString, QString> records;

void saveandclear( void );

int main() {

 QTextStream(stdout) << "[COM] FIND DOMAINS FOR ORPHANS" << endl;
 QString filename = "temp_COM/ALL";
 QFile file(filename);
 if(!file.open(QIODevice::ReadOnly)) {
  QTextStream(stdout) << "Could not open zone-file" << endl;
  return 1;
 }
 QTextStream data(&file);
 qint32 inside = 0;
 qint32 outside = 0;
 qint32 c = 0;
 qint32 uniqueoutside = 0;

 struct record {
   QString server;
   QString domain;
 };

 records.clear();

 while (!data.atEnd()) {
   QString line = data.readLine();
   if ((!(line.at(0)==';'))&&(!line.isEmpty())) {
     QStringList record = line.split(" ");
     if ((record.count()>2)&&(record.at(1).compare("NS")==0)) {
       //When we reach this we know it's an NS record.
       QString domain = record.at(0)+".COM"; //Add .COM to get complete domains for use in the next step
       QString server = record.at(2);
       if (server.endsWith(".")) { //Inside pointing records end without a dot
         server.chop(1); //Remove the dot at the end
         if (records.contains(server)) {
           QString domainlist = records.value(server)+"|"+domain;
           records.insert(server, domainlist);
         } else {
           /*QUrl server_url;
           server_url.setHost(server);
           QString tld = server_url.topLevelDomain();
           QTextStream(stdout) << "TLD:" << tld << " SERVER: " << server << endl; 
           return 0;*/
           records.insert(server, domain);
           uniqueoutside++; 
         }
         outside++;
       } else { inside++; }
     }
   }
   c++;
   if (c>100000) {
     c = 0;
     QTextStream(stdout) << "[" << QString::number(inside) << "/" << QString::number(outside) << ", "<<  QString::number(uniqueoutside) <<"]" << endl;
     //saveandclear();
   }
 }
 
 file.close();

 //Done!
 QTextStream(stdout) << "Inside: " << QString::number(inside) << endl;
 QTextStream(stdout) << "Outside: " << QString::number(outside) << endl;
 QTextStream(stdout) << "Unique outside: " << QString::number(uniqueoutside) << endl;

 saveandclear();
 return 0;
}

void saveandclear( void ) {
 QString outputfile_name="temp/OUTPUT_COM_"+QString::number(outputcounter)+".txt";
 QFile outputfile( outputfile_name );
 if ( outputfile.open(QIODevice::ReadWrite) ) {
   QTextStream output( &outputfile );
   QMap<QString, QString>::iterator i;
   for (i = records.begin(); i != records.end(); ++i) {
     output << i.key() << ":" << i.value() << endl;
   }
   outputfile.close();
   outputcounter++;
   records.clear();
 } else {
  QTextStream(stdout) << "COULD NOT OPEN OUTPUT FILE" << endl;
 }
}
