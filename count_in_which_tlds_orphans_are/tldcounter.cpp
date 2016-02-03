#include <QTextStream>
#include <QFile>
#include <QString>
#include <QMap>

int main() {
 QTextStream(stdout) << "Generating..." << endl;
 QString filename = "../output/orphans.txt";
 QFile file(filename);
 if(!file.open(QIODevice::ReadOnly)) { QTextStream(stdout) << "Could not open orphans.txt" << endl; return 1; }
 QTextStream data(&file);

 QString ofilename="../output/orphans_per_tld.txt";
 QFile ofile(ofilename);
 if ( !ofile.open(QIODevice::ReadWrite) ) { QTextStream(stdout) << "Could not open "<< ofilename << endl; return 1; }

 QTextStream output( &ofile );

 QMap<QString, qint32> tlds;

 while (!data.atEnd()) {
   QString line = data.readLine();
   QStringList record = line.split("|");
   QString tld = record.at(1);
   if (!tld.isEmpty()) {
     if(tlds.contains(tld)) {
       qint32 amount = tlds.value(tld);
       amount++;
       tlds.insert(tld, amount);
     } else {
       tlds.insert(tld, 1);
     }
   }
 }

 QMap<QString, qint32>::iterator i;
 for (i = tlds.begin(); i != tlds.end(); ++i) {
   output << i.key() << "," << QString::number(i.value()) << endl;
 }

 file.close();
 ofile.close();
 return 0;
}
