#include <string>
#include <sstream>
#include <vector>
#include <iostream>
#include <algorithm>
#include <iterator>

using namespace std;

int main() {
    string ss = "This is a test string";
    istringstream buf(ss);
    vector<string> item {istream_iterator<string>{buf}, istream_iterator<string>{}};
    int i = 0;
    while(item[i] != "\0") {
        for(int k = 0; k < item[i].length(); k++) {
            cout << item[i][k] << endl;
        }
        i++;
    }
}
