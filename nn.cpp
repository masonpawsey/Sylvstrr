//Zakary Worman
//Framework from CS 3560 during Lab7
//Using this neural network to create the Twitter Sentiment Neural Network


#include <iostream>
#include <fstream>
#include <vector>
#include <sstream>
#include <cmath>
#include <string>
#include <algorithm>
#include <iterator>

using namespace std;

struct Data {
    string text;
    int wordCount = 0;
    double pos = -1, neg = -1, neu = -1;
    unsigned long long hashedText[140] = {0};
    vector<string> words;

    void Hash() {
        text_to_words();
        for(unsigned int i = 0; i < words.size(); i++) {
            if(words[wordCount].length() > 9) {
                auto it = words.begin() + wordCount + 1;
                string temp = words[wordCount].substr(9);
                words.insert(it,temp);
                words[wordCount] = words[wordCount].substr(0,9);
            }
            for (unsigned int i = 0; i < words[wordCount].length(); i++) {
                if(words[wordCount][i] <= 126 && words[wordCount][i] >= 33) {
                    hashedText[wordCount] += pow(94, 9 - i - 1)*(1 + words[wordCount][i] - '!');
                }
            }
            wordCount++;
        }
    }
    void text_to_words() {
        istringstream iss(text);
        copy(istream_iterator<string>(iss), istream_iterator<string>(),
                back_inserter(words));
    }
};
struct Hidden {
    double w[140], theta;
    double y;
    double charge = 0, delta = 0, prevDelta = 0;

    double Activation() {
        //return(1.0/(1.0+exp(-charge)));
        return(2.0/(1.0+exp(-2.0*charge)) - 1.0);
    }
};
struct Output {
    double w[5], theta;
    double y;
    double charge = 0 , delta = 0, prevDelta = 0;

    double Activation() {
        //return(1.0/(1.0+exp(-charge)));
        return(2.0/(1.0+exp(-2.0*charge)) - 1.0);
    }
};
struct Layer {
    Hidden neuron[5];
};

int main(int argc, char *argv[]) {
    /* 
     * Code to load a CSV into a 2-D vector. No need to modify
     * the following code.
     */
    vector<vector<string>> data;	// Vector containing data and labels
    ifstream file("full-corpus.csv");			// File to load data
    srand(time(NULL));
    Layer layer1;
    Output conf[3];
    /* 
     * Part 0: Code to load in from a CSV, no need to edit this
     */
    unsigned int it = 0;
    string line;
    while(getline(file, line)) {
        vector<string> row;
        stringstream iss(line);

        string val;
        while (getline(iss, val, ',')) {
            try {
                row.push_back(val);
            }
            catch (...) {
                cout << "Bad input: " << val << endl;
            }
        }
        data.push_back(row);
        it++;
    }
    Data input[it];
    /* 
     * Part 1: Reflex agent
     */
    // Iterate over the array and make a decision based on some value.
    unsigned int i = 0;
    for (const auto &row : data) {
        // Each row represents a data sample.
        // The columns represent a specific feature.
        // For the Iris dataset:
        //	0 - Name of Keyword
        //	1 - Sentiment
        //	2 - TweetID
        //	3 - TweetDate
        // The final column (4) is the TweetText
        input[i].text = row.back();
        input[i].text = input[i].text.substr(1,input[i].text.length()-2);
        input[i].Hash();
        /*for(int f = 0; f < 13; f++) {
          input[i].x[f] = row[f];
          }*/
        if (row[1] == "\"positive\"")
            input[i].pos = 1;
        else if (row[1] == "\"negative\"")
            input[i].neg = 1;
        else 
            input[i].neu = 1;
        i++;
    }
    for(int k = 0; k < 5; k++) {
        for(int s = 0; s < 140; s++)
            layer1.neuron[k].w[s] = fmod(rand(),(2.4/it)*2.0) - 2.4/it;
        layer1.neuron[k].theta = fmod(rand(),(2.4/it)*2.0) - 2.4/it;
    }
    for(int k = 0; k < 3; k++) {
        for(int s = 0; s < 5; s++)
            conf[k].w[s] = fmod(rand(),(2.4/it)*2.0) - 2.4/it;
        conf[k].theta = fmod(rand(),(2.4/it)*2.0) - 2.4/it;
    }

    int epoch = 1;
    for(int l = 0; l < 500; l++) {
        double perCor = 0;
        double posCor = 0, negCor = 0, neuCor = 0;
        double pos = 0, neg = 0, neu = 0;
        double iteration = 0;
        double perf = 0;
        for(unsigned int r = 0; r < 3000; r++) {
            int k = rand()%it;
            if(input[k].pos == 1)
                pos++;
            if(input[k].neg == 1)
                neg++;
            if(input[k].neu == 1)
                neu++;
            for(int s = 0; s < 5; s++) {
                for(int q = 0; q < input[k].wordCount; q++) 
                    layer1.neuron[s].charge += input[k].hashedText[q]*layer1.neuron[s].w[q];
                layer1.neuron[s].charge -= layer1.neuron[s].theta;
            }
            layer1.neuron[0].y = layer1.neuron[0].Activation();
            layer1.neuron[1].y = layer1.neuron[1].Activation();
            for(int f = 0; f < 3; f++) {
                conf[f].charge = layer1.neuron[0].y*conf[f].w[0] + layer1.neuron[1].y*conf[f].w[1] - conf[f].theta;
            }
            conf[0].y = conf[0].Activation();
            conf[1].y = conf[1].Activation();
            conf[2].y = conf[2].Activation();

            cout << "Epoch " << epoch << ",\tIteration " << 1+iteration << ":\tPrediction is [" << conf[0].y << ", " 
                << conf[1].y << ", " << conf[2].y << "]" 
                << "\t\t[" << input[k].pos << ", " << input[k].neg << ", " << input[k].neu << "]" << endl;
            iteration++;
            perf = perf + abs(input[k].pos - conf[0].y) + abs(input[k].neg - conf[1].y) + abs(input[k].neu - conf[2].y);
            //calculate percentage correct
            if (conf[0].y >= conf[1].y && conf[0].y >= conf[2].y && input[k].pos == 1) {
                posCor++;
                perCor++;
            }
            else if (conf[1].y >= conf[0].y && conf[1].y >= conf[2].y && input[k].neg == 1) {
                negCor++;
                perCor++;
            }
            else if (conf[2].y >= conf[1].y && conf[2].y >= conf[0].y && input[k].neu == 1) {
                neuCor++;
                perCor++;
            }
            //conf[0].delta = conf[0].y*(1-conf[0].y)*(input[k].pos - conf[0].y);
            //conf[1].delta = conf[1].y*(1-conf[1].y)*(input[k].neg - conf[1].y);
            //conf[2].delta = conf[2].y*(1-conf[2].y)*(input[k].neu - conf[2].y);
            conf[0].prevDelta = conf[0].delta;
            conf[0].delta = (1-conf[0].y*conf[0].y)*(input[k].pos - conf[0].y);
            conf[1].prevDelta = conf[1].delta;
            conf[1].delta = (1-conf[1].y*conf[1].y)*(input[k].neg - conf[1].y);
            conf[0].prevDelta = conf[2].delta;
            conf[2].delta = (1-conf[2].y*conf[2].y)*(input[k].neu - conf[2].y);
            for(int s = 0; s < 3; s++) {
                for(int f = 0; f < 5; f++)  {
                    conf[s].w[f] = conf[s].w[f] + 0.9*conf[s].prevDelta + 0.05*layer1.neuron[f].y*conf[s].delta;
                }
                conf[s].theta = conf[s].theta + 0.9*conf[s].prevDelta + 0.05*(-1)*conf[s].delta;
            }
            //compute neuron delta
            double sum;
            for(int s = 0; s < 5; s++) {
                sum = 0;
                layer1.neuron[s].prevDelta = layer1.neuron[s].delta;
                layer1.neuron[s].delta = layer1.neuron[s].y*(1-layer1.neuron[s].y);
                for(int f = 0; f < 3; f++) {
                    sum += conf[f].delta * conf[f].w[s];
                }
                layer1.neuron[s].delta *= sum;
            }

            for(int s = 0; s < 5; s++) {
                for(int f = 0; f < input[k].wordCount; f++) {
                    layer1.neuron[s].w[f] = layer1.neuron[s].w[f] + 0.9*layer1.neuron[s].prevDelta 
                        + 0.05*input[k].hashedText[input[k].wordCount]*layer1.neuron[s].delta;
                }
                layer1.neuron[s].theta = layer1.neuron[s].theta  + 0.9*layer1.neuron[s].prevDelta
                    + 0.05*(-1)*layer1.neuron[s].delta;
            }
        }
        double MAD = perf/iteration;
        perCor /= iteration;
        perCor *= 100;
        posCor /= pos;
        posCor *= 100;
        negCor /= neg;
        negCor *= 100;
        neuCor /= neu;
        neuCor *= 100;
        cout << "EPOCH " << epoch << " Results: MAD = " << MAD << endl;
        cout << "Percentage Correct: " << perCor << "%" << endl;
        cout << "Percentage Pos Correct: " << posCor << "%" << endl;
        cout << "Percentage Neg Correct: " << negCor << "%" << endl;
        cout << "Percentage Neu Correct: " << neuCor << "%" << endl;
        epoch++;
    }
}
