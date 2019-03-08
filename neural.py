import numpy as np
from matplotlib import pyplot as plt
from scipy.stats import truncnorm

def truncated_normal(mean, sd, low, upp):
    return truncnorm((low - mean) / sd, (upp - mean) / sd, loc=mean, scale=sd)

def sigmoid(x):
    return 1 / (1 + np.exp(-x))

def sigmoid_derivative(x):
    return x * (1 - x)

def mean_error(x, y):
    return x - y

#lets me change activation functions quickly
def activation_function(x):
    return sigmoid(x)
def activation_function_derivative(x):
    return sigmoid_derivative(x)

#lets me change loss functions quickly
def loss_function(x, y):
    return mean_error(x, y)

class NeuralNetwork:

    def __init__(self, no_of_in_neur, no_of_out_neur, no_of_hidden_neur, no_of_hidden_layers, learning_rate, bias=None):  
        self.input_neurons  = no_of_in_neur
        self.output_neurons = no_of_out_neur
        self.hidden_neurons = [no_of_hidden_neur for i in range(no_of_hidden_layers)]
            
        self.alpha = learning_rate 
        self.bias  = bias

        #initialize weights and bias neuron(s)
        bias_node = 1 if self.bias else 0
        
        rad = 1 / np.sqrt(self.input_neurons + bias_node)    #randomizing wieghts between (-1/sqrt(n), 1/sqrt(n))
        X   = truncated_normal(0, 1, -rad, rad)              #gives a normal distribution of our choosing

        self.hidden_weights = [X.rvs((self.hidden_neurons[i], self.hidden_neurons[0] + bias_node)) for i in range(no_of_hidden_layers)]
        self.hidden_weights[0] = X.rvs((self.hidden_neurons[0], self.input_neurons + bias_node))

        rad = 1 / np.sqrt(self.hidden_neurons[0] + bias_node)
        X   = truncated_normal(0, 1, -rad, rad)

        self.output_weights = X.rvs((self.output_neurons, self.hidden_neurons[0] + bias_node))

        self.hidden_layer = no_of_hidden_layers - 1
        

    def train(self, input_vector, target_vector):
        # input_vector and target_vector can be tuple, list or ndarray
        bias_node = 1 if self.bias else 0
        if self.bias:
            # adding bias node to the end of the input_vector
            input_vector = np.concatenate((input_vector, [self.bias]))
                                    
        input_vector  = np.array(input_vector, ndmin=2).T     #np.array with min diminsions = 2
        target_vector = np.array(target_vector, ndmin=2).T    #finish vectorizing inputs
        
        #FeedForward------------------------------------------------------------------
        output_vector1          = np.dot(self.hidden_weights[0], input_vector)
        output_vector_hidden    = [activation_function(output_vector1) for i in range(self.hidden_layer+1)]
        if self.bias:
            output_vector_hidden[0] = np.concatenate((output_vector_hidden[0], [[self.bias]]))

        i = 1
        while i < self.hidden_layer:
            output_vector1          = np.dot(self.hidden_weights[i], output_vector_hidden[i])
            output_vector_hidden[i] = activation_function(output_vector1)
            i += 1
        
            if self.bias:
                output_vector_hidden[i] = np.concatenate((output_vector_hidden[i], [[self.bias]]))

        output_vector2        = np.dot(self.output_weights, output_vector_hidden[self.hidden_layer])
        output_vector_network = activation_function(output_vector2)
        
        #BackProp----------------------------------------------------------------------
        output_errors = loss_function(target_vector, output_vector_network)
        # update the weights:
        output_delta = output_errors * activation_function_derivative(output_vector_network)
        output_dw    = self.alpha  * np.dot(output_delta, output_vector_hidden[self.hidden_layer].T)

        self.output_weights += output_dw
        
        if self.hidden_layer > 0:
            # calculate hidden errors for last hidden layer:
            hidden_errors = np.dot(self.output_weights.T, output_errors)

            # update the weights:
            hidden_delta = hidden_errors * activation_function_derivative(output_vector_hidden[self.hidden_layer])

            if self.bias:
                hidden_dw = np.dot(hidden_delta, output_vector_hidden[self.hidden_layer-1].T)[:-1,:] #do not want to train the bias neuron
            else:
                hidden_dw = np.dot(hidden_delta, output_vector_hidden[self.hidden_layer-1].T)

            self.hidden_weights[self.hidden_layer] += self.alpha * hidden_dw

            i = self.hidden_layer - 1
            while i > 0:
                # calculate hidden errors for middle hidden layer:
                hidden_errors = np.dot(self.hidden_weights[i+1].T, hidden_errors)

                # update the weights:
                hidden_delta = hidden_errors * activation_function_derivative(output_vector_hidden[i])

                if self.bias:
                    hidden_dw = np.dot(hidden_delta, output_vector_hidden[i-1].T)[:-1,:]     #do not want to train the bias neuron
                else:
                    hidden_dw = np.dot(hidden_delta, output_vector_hidden[i-1].T)

                self.hidden_weights[i] += self.alpha * hidden_dw
                i -= 1

            # calculate hidden errors for first hidden layer:
            hidden_errors = np.dot(self.hidden_weights[1].T, hidden_errors)

            # update the weights:
            hidden_delta = hidden_errors * activation_function_derivative(output_vector_hidden[0])

            if self.bias:
                hidden_dw = np.dot(hidden_delta, input_vector.T)[:-1,:]     #do not want to train the bias neuron
            else:
                hidden_dw = np.dot(hidden_delta, input_vector.T)

            self.hidden_weights[0] += self.alpha * hidden_dw
        else:
            # calculate hidden errors for last hidden layer:
            hidden_errors = np.dot(self.output_weights.T, output_errors)

            # update the weights:
            hidden_delta = hidden_errors * activation_function_derivative(output_vector_hidden[self.hidden_layer])

            if self.bias:
                hidden_dw = np.dot(hidden_delta, input_vector.T)[:-1,:]     #do not want to train the bias neuron
            else:
                hidden_dw = np.dot(hidden_delta, input_vector.T)

            self.hidden_weights[0] += self.alpha * hidden_dw

    def run(self, input_vector):
        # input_vector can be tuple, list or ndarray
        
        if self.bias:
            # adding bias node to the end of the inpuy_vector
            input_vector = np.concatenate((input_vector, [1]))

        input_vector  = np.array(input_vector, ndmin=2).T
        output_vector = np.dot(self.hidden_weights, input_vector)
        output_vector = activation_function(output_vector)
        
        if self.bias:
            output_vector = np.concatenate((output_vector, [[1]]))
            
        output_vector = np.dot(self.output_weights, output_vector)
        output_vector = activation_function(output_vector)
    
        return output_vector

if __name__ == "__main__":
    class1 = [(3, 4), (4.2, 5.3), (4, 3), (6, 5), (4, 6), (3.7, 5.8),
              (3.2, 4.6), (5.2, 5.9), (5, 4), (7, 4), (3, 7), (4.3, 4.3) ] 
    class2 = [(-3, -4), (-2, -3.5), (-1, -6), (-3, -4.3), (-4, -5.6), 
              (-3.2, -4.8), (-2.3, -4.3), (-2.7, -2.6), (-1.5, -3.6), 
              (-3.6, -5.6), (-4.5, -4.6), (-3.7, -5.8) ]
    pos = np.random.randint(0, 50, (140, 280))
    neg = np.random.randint(100, 150, (140, 280))
    neu = np.random.randint(200, 250, (140, 280))

    labeled_data = []
    for el in pos:
        labeled_data.append([el, [1, 0, 0]])
    for el in neg:
        labeled_data.append([el, [0, 1, 0]])
    for el in neu:
        labeled_data.append([el, [0, 0, 1]])

    np.random.shuffle(labeled_data)

    data, labels = zip(*labeled_data)
    labels = np.array(labels)
    data = np.array(data)

    simple_network = NeuralNetwork(280, 3, 100, 1, 0.1, 1)
    
    for epoch in range(20):
        for i in range(len(data)):
            simple_network.train(data[i], labels[i])
    for i in range(len(data)):
        print(labels[i])
        print(simple_network.run(data[i]))
