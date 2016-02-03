#include <QTextStream>
#include <QFile>
#include <QString>

int main() {

 QTextStream(stdout) << "Generating..." << endl;
 QString filename = "../output/orphans.txt";
 QFile file(filename);
 if(!file.open(QIODevice::ReadOnly)) { QTextStream(stdout) << "Could not open orphans.txt" << endl; return 1; }
 QTextStream data(&file);
 QString fname_ip4="temp/ip4nodedup.txt";
 QString fname_ip6="temp/ip6nodedup.txt";

 QFile f_ip4(fname_ip4);
 QFile f_ip6(fname_ip6);
 if ( !f_ip4.open(QIODevice::ReadWrite) ) { QTextStream(stdout) << "Could not open "<< fname_ip4 << endl; return 1; }
 if ( !f_ip6.open(QIODevice::ReadWrite) ) { QTextStream(stdout) << "Could not open "<< fname_ip6 << endl; return 1; }

 QTextStream s_ip4( &f_ip4 );
 QTextStream s_ip6( &f_ip6 );

 while (!data.atEnd()) {
   QString line = data.readLine();
   QStringList record = line.split("|");
   QString input4 = record.at(3);
   QString input6 = record.at(4);
   if (!input4.isEmpty()) {
     QStringList ip4list = input4.split(",");
     foreach(QString ip4, ip4list) {
       if (!ip4.isEmpty()) {
         //QTextStream(stdout) << "4: "<< ip4 << endl;
         s_ip4 << ip4 << endl;
       }
     }
   }
   if (!input6.isEmpty()) {
     QStringList ip6list = input6.split(",");
     foreach(QString ip6, ip6list) {
       if (!ip6.isEmpty()) {
         //QTextStream(stdout) << "6: "<< ip6 << endl;
         s_ip6 << ip6 << endl;
       }
     }
   }
 }
 file.close();
 f_ip4.close();
 f_ip6.close();
 return 0;
}
